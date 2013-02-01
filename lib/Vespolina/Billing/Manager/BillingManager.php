<?php
/**
 * (c) 2013 Vespolina Project http://www.vespolina-project.org
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Vespolina\Billing\Manager;

use Vespolina\Billing\Gateway\BillingGatewayInterface;
use Vespolina\Entity\Billing\BillingAgreement;
use Vespolina\Entity\Billing\BillingAgreementInterface;
use Vespolina\Entity\Order\ItemInterface;
use Vespolina\Entity\Order\OrderInterface;
use Vespolina\Entity\Partner\PartnerInterface;
use Vespolina\Entity\Pricing\PricingContextInterface;
use Vespolina\EventDispatcher\EventDispatcherInterface;
use Vespolina\EventDispatcher\NullDispatcher;
use Vespolina\Exception\InvalidConfigurationException;
use Vespolina\Entity\Order\OrderEvents;

class BillingManager implements BillingManagerInterface
{
    protected $billingAgreementClass;
    protected $billingRequestClass;
    protected $contexts;
    protected $eventDispatcher;
    protected $gateway;

    public function __construct(BillingGatewayInterface $gateway, array $classMapping, array $contexts, EventDispatcherInterface $eventDispatcher = null)
    {
        $missingClasses = array();
        foreach (array('billingAgreement', 'billingRequest') as $class) {
            $class = $class . 'Class';
            if (isset($classMapping[$class])) {

                if (!class_exists($classMapping[$class]))
                    throw new InvalidConfigurationException(sprintf("Class '%s' not found as '%s'", $classMapping[$class], $class));

                $this->{$class} = $classMapping[$class];
                continue;
            }
            $missingClasses[] = $class;
        }

        if (count($missingClasses)) {
            throw new InvalidConfigurationException(sprintf("The following billing classes are missing from configuration: %s", join(', ', $missingClasses)));
        }

        if (!$eventDispatcher) {
            $eventDispatcher = new NullDispatcher();
        }

        $this->eventDispatcher = $eventDispatcher;
        $this->gateway = $gateway;
        foreach ($contexts as $contextClass) {
            $context = new $contextClass();
            $process = $context['process'];
            $paymentType = $context['paymentType'];

            $this->context[$process][$paymentType] = $context;
        }
    }

    /**
     * @inheritdoc
     */
    public function processOrder(OrderInterface $order)
    {
        $billingAgreements = $this->createBillingAgreements($order);

        $event = $this->eventDispatcher->createEvent($order);
        $this->eventDispatcher->dispatch(OrderEvents::ACTIVATE_OR_RENEW_ITEMS, $event);

        // order already processed
        if (count($billingAgreements) == 0) {
            return false;
        }

        return true;
     }

    /**
     * @inheritdoc
     */
    public function createBillingAgreements(OrderInterface $order)
    {
        $billingAgreements = array();

        $recurring = array();
        /** @var $item \Vespolina\Entity\Order\ItemInterface */
        foreach ($order->getItems() as $item) {
            $pricingSet = $item->getPricing();

            // hack to initialize the entity and retrieve it from database
            $pricingSet->getProcessed();

            if ($pricingSet->get('recurringCharge')) {
                $agreement = $this->addItemToAgreements($item, $billingAgreements);
                $agreement
                    ->setPaymentProfile($order->getPartner()->getPreferredPaymentProfile())
                    ->setPartner($order->getPartner());
            }
        }

        return $billingAgreements;
    }

    protected function addItemToAgreements(ItemInterface $item, array &$agreements)
    {
        $pricingSet = $item->getPricing();
        $interval = $pricingSet->get('interval');
        $cycles = $pricingSet->get('cycles');
        $startsOn = $pricingSet->get('startsOn')->getTimestamp();

        $activeAgreement = null;
        foreach ($agreements as $agreement) {
            if ($agreement->getBillingInterval() == $interval &&
                $agreement->getBillingCycles() == $cycles &&
                $agreement->getInitialBillingDate()->getTimestamp() == $startsOn) {
                $activeAgreement = $agreement;
            }
        }

        if (!$activeAgreement) {
            $activeAgreement = new BillingAgreement();
            $activeAgreement
                ->setInitialBillingDate($pricingSet->get('startsOn'))
                ->setNextBillingDate($pricingSet->get('startsOn'))
                ->setBillingCycles($pricingSet->get('cycles'))
                ->setBillingInterval($pricingSet->get('interval'));
            ;
            $agreements[] = $activeAgreement;
        }

        $activeAgreement->addOrderItem($item);
        $activePricingSet = $activeAgreement->getPricing();
        $activeAgreement->setPricing($pricingSet->plus($activePricingSet));

        $this->gateway->persistBillingAgreement($activeAgreement);

        return $activeAgreement;
    }

    /**
     * @inheritdoc
     */
    function createBillingRequest(BillingAgreementInterface $billingAgreement)
    {
        if (!$billingAgreement->getActive()) {
            return false;
        }

        $paymentType = $billingAgreement->getPaymentProfile()->getType();
        $context = $this->context['billingRequest'][$paymentType];
        $partner = $billingAgreement->getPartner();
        $context['partner'] = $partner;
        $relatedAgreements = $this->findBillingAgreements($context);

        $billingRequest = new $this->billingRequestClass();
        $requestPricingSet = null;
        foreach ($relatedAgreements as $agreement) {
            $billingRequest->mergeOrderItems($agreement->getOrderItems());
            $requestPricingSet = $agreement->getPricing()->plus($requestPricingSet);
        }
        $billingRequest->setPricing($requestPricingSet);
        $billingRequest->setDueDate($agreement->getNextBillingDate());
        $billingRequest->setPartner($partner);

        $this->gateway->persistBillingRequest($billingRequest);

        return $requestPricingSet;
    }

    /**
     * @inheritdoc
     */
    public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        return $this->doFindBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * Finds billing agreements that are due
     *
     * @param $context
     * @param $limit
     * @param int $page
     * @return array
     */
    public function findBillingAgreements(PricingContextInterface $context, $limit = null, $page = 1)
    {
        $offset = ($page - 1) * $limit;

        /** @var \Molino\Doctrine\ORM\SelectQuery $query  */
        $query = $this->gateway->createQuery('select');
        $endDate = new \DateTime($context['endDate']);
        $query->filterLessEqual('nextBillingDate', $endDate);
        if ($context['startDate']) {
            $startDate = new \DateTime($context['startDate']);
            $query->filterGreater('nextBillingDate', $startDate);
        }
        if ($context['partner']) {
            $query->filterEqual('partner', $context['partner']);
        }
        $query->filterEqual('active', 1);
        if ($limit) {
            $query->limit($limit);
        }
        if ($offset) {
            $query->skip($offset);
        }

        return $query->all();
    }

    /**
     * Finds billing agreements that are due
     *
     * @param $context
     * @param $limit
     * @param int $page
     * @return array
     */
    public function findEligibleBillingAgreements($limit = null, $page = 1)
    {
        $offset = ($page - 1) * $limit;

        /** @var \Molino\Doctrine\ORM\SelectQuery $query  */
        $query = $this->gateway->createQuery('select');
        $qb = $query->getQueryBuilder();

        $now = new \DateTime();

        return $qb
            ->andWhere('m.active = ?1')
            ->andWhere('m.nextBillingDate <= ?2')
            ->setParameters(array(
                    1 => 1,
                    2 => $now->format('Y-m-d H:i:s')
                ))
            ->setFirstResult($offset)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult()
        ;
    }



    /**
     * @todo add implementation, please don't forget to call $em->clear() after each batch
     * @param array $billingAgreements
     */
    public function processEligibleBillingAgreements(array $billingAgreements)
    {

    }

    /**
     * @inheritdoc
     */
    protected function doFindBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        /** @var \Molino\Doctrine\ORM\SelectQuery $query  */
        $query = $this->gateway->createQuery('Select');
        $qb = $query->getQueryBuilder();

        foreach($criteria as $field => $value) {
            $qb->andWhere("m.$field = :$field");
            $qb->setParameter("$field", $value);
        }
        if ($orderBy) {
            $qb->orderBy($orderBy);
        }
        if ($limit) {
            $qb->setMaxResults($limit);
        }
        if ($offset) {
            $qb->setFirstResult($offset);
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * @param array $criteria
     * @param array $orderBy
     * @param null $limit
     * @param null $offset
     * @return null
     */
    public function doFindOneBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        $result = $this->doFindBy($criteria, $orderBy, $limit, $offset);
        if (count($result)) {
            return $result[0];
        }

        return null;
    }

    /**
     * @param $partner
     * @return array
     */
    public function findBillingAgreementsForPartner($partner)
    {
        return $this->doFindBy(array(
            'partner' => $partner
        ));
    }

    /**
     * @param $partner
     * @return int
     */
    public function getMonthlyTotalForPartner($partner)
    {
        $billingAgreements = $this->findBillingAgreementsForPartner($partner);

        $total = 0;
        foreach ($billingAgreements as $billingAgreement) {
            /** @var $billingAgreement BillingAgreement */
            if ($billingAgreement->getBillingInterval() == 'month') {
                $total += $billingAgreement->getBillingAmount();
            }
        }

        return $total;
    }

    /**
     * @param $orderItem
     * @return BillingAgreement
     */
    public function findBillingAgreementForItem(ItemInterface $orderItem)
    {
        /** @var \Molino\Doctrine\ORM\SelectQuery $query  */
        $query = $this->gateway->createQuery('Select');
        $qb = $query->getQueryBuilder();

        $qb
            ->join('m.orderItems', 'i')
            ->where('i.id = :item')
            ->setParameter('item', $orderItem->getId())
        ;

        return $qb->getQuery()->getOneOrNullResult();
    }
}
