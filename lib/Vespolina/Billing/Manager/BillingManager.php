<?php
/**
 * (c) 2013 Vespolina Project http://www.vespolina-project.org
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Vespolina\Billing\Manager;

use Vespolina\Billing\Gateway\BillingGatewayInterface;
use Vespolina\Billing\Process\DefaultBillingProcess;
use Vespolina\Entity\Billing\BillingAgreementInterface;
use Vespolina\Entity\Billing\BillingRequestInterface;
use Vespolina\Entity\Partner\PartnerInterface;
use Vespolina\Entity\Partner\PaymentProfileInterface;
use Vespolina\EventDispatcher\EventDispatcherInterface;
use Vespolina\Exception\InvalidConfigurationException;

class BillingManager implements BillingManagerInterface
{
    protected $billingAgreementClass;
    protected $billingRequestClass;
    protected $contexts;
    protected $defaultBillingProcessClass;
    protected $eventDispatcher;
    protected $gateway;

    public function __construct(BillingGatewayInterface $gateway, array $classMapping, array $contexts, EventDispatcherInterface $eventDispatcher = null)
    {
        $missingClasses = array();
        foreach (array('billingAgreement', 'billingRequest', 'defaultBillingProcess') as $class) {
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

    public function executeBillingRequest(BillingRequestInterface $billingRequest)
    {

    }

    /**
     * @param integer $id
     * @return BillingAgreementInterface
     */
    public function findBillingAgreementById($id)
    {
        /** @var \Molino\Doctrine\ORM\SelectQuery $q  */
        $q = $this->gateway->createQuery('select', $this->billingAgreementClass);

        return $q
            ->filterEqual('id', $id)
            ->one()
        ;
    }
    
    /**
     * @inheritdoc
     */
    public function generateBillingRequest(BillingAgreementInterface $billingAgreement)
    {
        // todo: retrieve the generate service for this billing agreement and use generator to generate the BR
        $billingRequest = new $this->billingRequestClass();

        return $billingRequest;
    }

    /**
     * @inheritdoc
     */
    public function initializeBilling($entity)
    {
        // todo use PaymentProfile handler
        $billingProcess = new $this->defaultBillingProcessClass($this,  $this->eventDispatcher);
        list($billingAgreements, $billingRequests) =  $billingProcess->prepareBilling($entity);

        return $billingProcess->executeBilling($billingAgreements, $billingRequests);
    }

    /**
     * @param \Vespolina\Entity\Billing\BillingAgreementInterface $ba
     */
    public function deactivateBillingAgreement(BillingAgreementInterface $ba)
    {
        $ba->setActive(0);
        $this->gateway->updateBillingAgreement($ba);
    }

    public function createBillingAgreement()
    {
        $billingAgreement = new $this->billingAgreementClass();
        $billingAgreement->setInitialBillingDate(new \DateTime());

        return $billingAgreement;
    }

    /**
    private function processPaidBillingRequest(BillingRequestInterface $br)
    {
        $orders = array();

        foreach ($br->getOrderItems() as $orderItems) {
            foreach ($orderItems as $item) {

                $order = $item->getParent();

                if (!isset($orders[$order->getId()])) {
                    $orders[$order->getId()] = $order;
                }

            }
            break;
        }

        if (!empty($orders)) {
            foreach ($orders as $o) {
                $event = $this->eventDispatcher->createEvent($o);
                $this->eventDispatcher->dispatch(OrderEvents::ACTIVATE_OR_RENEW_ITEMS, $event);
            }
        }
    }

    private function findPendingBillingRequests($page = 1)
    {
        $offset = ($page - 1) * self::BILLING_REQUEST_GET_LIMIT;

        $q = $this->gateway->createQuery('select', $this->billingRequestClass);

        $qb = $q->getQueryBuilder();

        return $qb
            ->andWhere('m.status = ?1')
            ->setParameters(array(
                1 => BillingRequest::STATUS_PENDING
            ))
            ->setMaxResults(self::BILLING_REQUEST_GET_LIMIT)
            ->setFirstResult($offset)
            ->getQuery()
            ->getResult()
        ;
    } */
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
        $endDate = new \DateTime($context['endDate']);
        $params = array(1 => $endDate);

        /** @var \Molino\Doctrine\ORM\SelectQuery $query  */
        $query = $this->gateway->createQuery('select', $this->billingAgreementClass);

        $qb = $query->getQueryBuilder();
        $qb->join('m.paymentProfile', 'pp');

        $qb->andWhere('m.nextBillingDate <= ?1');

        if (isset($context['partner'])) {
            $qb->andWhere('m.partner = ?3');
            $params[3] = $context['partner'];
        }

        $qb->andWhere('m.active = ?4');
        $params[4] = 1;

        if ($limit) {
            $qb
                ->setMaxResults($limit)
                ->setFirstResult($offset)
            ;
        }

        return $qb
            ->setParameters($params)
            ->getQuery()
            ->getResult()
        ;
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
     * Finds billing agreements for the given user/partner
     *
     * @param PartnerInterface $partner
     * @return array
     */
    /**
    public function findBillingAgreementsOnCurrentMonthForPartner(PartnerInterface $partner)
    {
        $query = $this->gateway->createQuery('Select', $this->billingAgreementClass);
        $qb = $query->getQueryBuilder();

        return $qb->where('m.partner = :partner')
            ->andWhere($qb->expr()->andX(
                $qb->expr()->eq('m.active', true),
                $qb->expr()->lte(':now', 'm.nextBillingDate'),
                $qb->expr()->lt('m.nextBillingDate', ':future')
            ))
            ->setParameters(array(
                'owner' => $partner,
                'now'     => new \DateTime('now'),
                'future'  => new \DateTime('+ 1 month')
            ))
            ->getQuery()
            ->getResult()
        ;
    } */

    /**
     * @param $orderItem
     * @return BillingAgreement
     */
    /**
    public function findBillingAgreementForItem(ItemInterface $orderItem)
    {
        $query = $this->gateway->createQuery('Select', $this->billingAgreementClass);
        $qb = $query->getQueryBuilder();

        $qb
            ->join('m.orderItems', 'i')
            ->where('i.id = :item')
            ->setParameter('item', $orderItem->getId())
        ;

        return $qb->getQuery()->getOneOrNullResult();
    } */

    public function updateBillingAgreement(BillingAgreementInterface $billingAgreement)
    {
        $this->gateway->updateBillingAgreement($billingAgreement);
    }


    public function updateBillingRequest(BillingRequestInterface $billingRequest)
    {
        $this->gateway->updateBillingRequest($billingRequest);
    }
}
