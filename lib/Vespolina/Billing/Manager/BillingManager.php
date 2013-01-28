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
use Vespolina\Entity\Order\OrderInterface;
use Vespolina\Entity\Partner\PartnerInterface;
use Vespolina\EventDispatcher\EventDispatcherInterface;
use Vespolina\EventDispatcher\NullDispatcher;
use Vespolina\Exception\InvalidConfigurationException;
use Vespolina\Entity\Order\OrderEvents;

class BillingManager implements BillingManagerInterface
{
    protected $cartClass;
    protected $eventDispatcher;
    protected $gateway;

    public function __construct(BillingGatewayInterface $gateway, array $classMapping, EventDispatcherInterface $eventDispatcher = null)
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

        /** @var $item \Vespolina\Entity\Order\ItemInterface */
        foreach ($order->getItems() as $item) {
            $pricingSet = $item->getPricing();

            // hack to initialize the entity and retrieve it from database
            $pricingSet->getProcessed();

            // @iampersistant: at this point I am stuck: what is recurringCharge? where do we set it it at item level, in processed() method ?
            $recurringCharge = $pricingSet->get('recurringCharge');

            // Theo: starts in and interval at PricingSet level ?!?!
            $startOn = '+1 ' . $pricingSet->get('interval');
            $startDate = new \DateTime($startOn);

            $billingAgreement = new BillingAgreement();
            $billingAgreement
                ->setPaymentGateway($order->getAttribute('payment_gateway'))
                ->setPartner($order->getPartner())
                ->setInitialBillingDate(new \DateTime('now'))
                ->setNextBillingDate($startDate)
                ->setBillingAmount($recurringCharge)
                ->setOrderItem($item)
            ;
            $billingAgreements[] = $billingAgreement;
        }

        return $billingAgreements;
    }

    /**
     * @inheritdoc
     */
    function createBillingRequest(PartnerInterface $partner)
    {
        $orderArray = $this->orderManager->findClosedOrdersByOwner($partner);
        //$orderArray->toArray();
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
     * @param $limit
     * @param int $page
     * @return array
     */
    public function findEligibleBillingAgreements($limit, $page = 1)
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

    public function findBillingAgreementForItem($orderItem)
    {
        $this->doFindBy(array('orderItem' => $orderItem));
    }
}
