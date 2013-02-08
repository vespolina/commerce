<?php
/**
 * (c) 2013 Vespolina Project http://www.vespolina-project.org
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Vespolina\Billing\Manager;

use Vespolina\Billing\Gateway\BillingGatewayInterface;
use ImmersiveLabs\CaraCore\Entity\License;
use Vespolina\Entity\Billing\BillingAgreement;
use Vespolina\Entity\Billing\BillingAgreementInterface;
use Vespolina\Entity\Order\ItemInterface;
use Vespolina\Entity\Order\OrderInterface;
use Vespolina\Entity\Partner\PartnerInterface;
use Vespolina\Entity\Pricing\PricingContext;
use Vespolina\Entity\Pricing\PricingContextInterface;
use Vespolina\EventDispatcher\EventDispatcherInterface;
use Vespolina\EventDispatcher\NullDispatcher;
use Vespolina\Exception\InvalidConfigurationException;
use Vespolina\Entity\Order\OrderEvents;
use Vespolina\Entity\Partner\PaymentProfile;
use Vespolina\Entity\Billing\BillingRequest;
use Vespolina\Entity\Partner\PaymentProfileType\CreditCard;
use Vespolina\Entity\Partner\PaymentProfileType\Invoice;
use JMS\Payment\CoreBundle\Entity\ExtendedData;
use JMS\Payment\CoreBundle\PluginController\Result;
use Vespolina\Entity\Order\Item;
use Vespolina\Entity\Partner\Partner;

class BillingManager implements BillingManagerInterface
{
    const BILLING_REQUEST_GET_LIMIT = 100;

    /** @var \ImmersiveLabs\CaraCore\Manager\UserManager */
    protected $userManager;
    /** @var BillingInvoiceManager */
    protected $billingInvoiceManager;
    /** @var \ImmersiveLabs\BillingBundle\Service\BillingService */
    protected $billingService;
    /** @var \Vespolina\Invoice\Manager\InvoiceManager */
    protected $invoiceManager;
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
     * @param integer $id
     * @return BillingAgreement
     */
    public function findBillingAgreementById($id)
    {
        /** @var \Molino\Doctrine\ORM\SelectQuery $q  */
        $q = $this->gateway->createQuery('select');

        return $q
            ->filterEqual('id', $id)
            ->one()
        ;
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
     * @param \Vespolina\Entity\Billing\BillingAgreement $ba
     */
    public function deactivateBillingAgreement(BillingAgreement $ba)
    {
        $ba->setActive(0);
        $this->gateway->updateBillingAgreement($ba);
    }

    /**
     * @inheritdoc
     */
    public function createBillingAgreements(OrderInterface $order)
    {
        $partner = $order->getPartner();
        $paymentProfile = $partner->getPreferredPaymentProfile();

        $paymentProfileType = $paymentProfile->getType();
        $context = $this->context['billingAgreement'][$paymentProfileType];

        $billingAgreements = $this->prepBillingAgreements($context, $partner, $paymentProfile, $order->getItems());

        return $billingAgreements;
    }

    /**
     * Removes licenses from their respective billing agreements. This forces recreation
     * of billing agreements to recompute
     *
     * @param array $licenses
     */
    public function removeLicensesFromBillingAgreement(array $licenses)
    {
        $licensesMap = $this->mapLicensesByBillingAgreements($licenses);

        foreach ($licensesMap as $baId => $licensesToRemove) {
            $ba = $this->findBillingAgreementById($baId);

            $orderItems = $ba->getOrderItems();

            $finalOrderItems = array();

            foreach ($orderItems as $item) {
                /** @var Item $item */
                if (!in_array($item->getAttribute('reference'), $licensesToRemove)) {
                    $finalOrderItems[] = $item;
                }
            }

            if (empty($finalOrderItems)) {
                $this->deactivateBillingAgreement($ba);
            } else {
                $this->recreateBillingAgreement($ba, $finalOrderItems);
            }
        }
    }

    /**
     * Rebuilds the billing agreement if there are any adjustments to the old agreement
     *
     * @param \Vespolina\Entity\Billing\BillingAgreement $oldAgreement
     * @param $orderItems
     * @return array
     */
    public function recreateBillingAgreement(BillingAgreement $oldAgreement, $orderItems)
    {
        $this->deactivateBillingAgreement($oldAgreement);

        /** @var PaymentProfile $paymentProfile  */
        $paymentProfile = $oldAgreement->getPaymentProfile();

        return $this->recreateBillingAgreementByPartnerAndPaymentProfile($oldAgreement->getPartner(), $paymentProfile, $orderItems);
    }

    /**
     * @param \Vespolina\Entity\Partner\Partner $partner
     * @param \Vespolina\Entity\Partner\PaymentProfile $paymentProfile
     * @param $orderItems
     * @return array
     */
    public function recreateBillingAgreementByPartnerAndPaymentProfile(Partner $partner, PaymentProfile $paymentProfile, $orderItems)
    {
        $paymentProfileType = $paymentProfile->getType();

        $context = $this->context['billingAgreement'][$paymentProfileType];

        return $this->prepBillingAgreements($context, $partner, $paymentProfile, $orderItems);
    }

    /**
     * @param $context
     * @param \Vespolina\Entity\Partner\Partner $partner
     * @param \Vespolina\Entity\Partner\PaymentProfile $paymentProfile
     * @param $orderItems
     * @return array
     */
    private function prepBillingAgreements($context, Partner $partner, PaymentProfile $paymentProfile, $orderItems)
    {
        $billingAgreements = array();

        /** @var Item $item **/
        foreach ($orderItems as $item) {
            $pricingSet = $item->getPricing();

            $pricingSet->getProcessed();

            if ($pricingSet->get('recurringCharge')) {
                $agreement = $this->addItemToAgreements($item, $billingAgreements, $context);
                $agreement
                    ->setPaymentProfile($paymentProfile)
                    ->setPartner($partner)
                ;

                $this->gateway->updateBillingAgreement($agreement);
            }
        }

        return $billingAgreements;
    }

    protected function addItemToAgreements(ItemInterface $item, array &$agreements, PricingContextInterface $context)
    {
        $pricingSet = $item->getPricing();
        $interval = $pricingSet->get('interval');
        $cycles = $pricingSet->get('cycles');

        if ($item->getAttribute('start_billing')) {
            $startsOn = $item->getAttribute('start_billing');
        } elseif ($context['dueDate']) {
            $startsOn = $pricingSet->get('startsOn');
            $date = explode(',', $startsOn->format('Y,m'));
            $startsOn->setDate($date[0], $date[1], $context['dueDate']);
        } else {
            $startsOn = $pricingSet->get('startsOn');
        }
        $startTimestamp = $startsOn->getTimestamp();

        $activeAgreement = null;
        foreach ($agreements as $agreement) {
            if ($agreement->getBillingInterval() == $interval &&
                $agreement->getBillingCycles() == $cycles &&
                $agreement->getInitialBillingDate()->getTimestamp() == $startTimestamp) {
                $activeAgreement = $agreement;
            }
        }

        if (!$activeAgreement) {
            $activeAgreement = new BillingAgreement();
            $activeAgreement
                ->setInitialBillingDate($startsOn)
                ->setNextBillingDate($startsOn)
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

    public function processPendingBillingRequests()
    {
        $page = 1;
        do {
            $billingRequests = $this->findPendingBillingRequests($page);

            foreach ($billingRequests as $br) {
                /** @var BillingRequest $br */
                $paymentProfile = $br->getPaymentProfile();
                $isProcessItems = false;
                if ($paymentProfile instanceof CreditCard) {
                    $totalValue = $br->getPricingSet()->get('totalValue');
                    $ex = new ExtendedData();
                    $ex->set('cardId', $paymentProfile->getReference(), false, false);
                    $result = $this->getBillingService()->doStraightPayment($totalValue, $ex);
                    if ($result->getStatus() == Result::STATUS_SUCCESS) {
                        $br->setStatus(BillingRequest::STATUS_PAID);
                        $inv = new \Vespolina\Entity\Invoice\Invoice();
                        $inv
                            ->setPartner($br->getPartner())
                            ->setDueDate($br->getDueDate())
                            ->setIssuedDate($br->getCreatedAt())
                            ->setPayment($totalValue)
                            ->setPeriodStart(new \DateTime())
                            ->setPeriodEnd(new \DateTime('+1 month')) //@todo confirm what's the purpose of period start and period end
                        ;

                        $this->getInvoiceManager()->updateInvoice($inv);

                        $br->setInvoice($inv);

                        $isProcessItems = true;
                    }
                } elseif ($paymentProfile instanceof Invoice) {
                    $user = $this->getUserManager()->findOneBy(array('partner' => $br->getPartner()));
                    $this->getBillingInvoiceManager()->sendNotification($user, $br);
                    $br->setStatus(BillingRequest::STATUS_INVOICE_SENT);

                    $isProcessItems = true;
                }

                $this->gateway->updateBillingRequest($br);

                if ($isProcessItems) {
                    $this->processPaidBillingRequest($br);
                }
            }

            $page++;
        } while(count($billingRequests) == self::BILLING_REQUEST_GET_LIMIT);
    }

    private function processPaidBillingRequest(BillingRequest $br)
    {
        $orders = array();

        foreach ($br->getOrderItems() as $orderItems) {
            foreach ($orderItems as $item) {
                /** @var Item $item */

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

        /** @var \Molino\Doctrine\ORM\SelectQuery $q  */
        $q = $this->gateway->createQuery('select', '\Vespolina\Entity\Billing\BillingRequest');

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
            $agreement->completeCurrentCycle($billingRequest);
            $this->gateway->updateBillingAgreement($agreement);
        }
        $billingRequest->setPaymentProfile($billingAgreement->getPaymentProfile());
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
        $endDate = new \DateTime($context['endDate']);
        $params = array(1 => $endDate);

        /** @var \Molino\Doctrine\ORM\SelectQuery $query  */
        $query = $this->gateway->createQuery('select');

        $qb = $query->getQueryBuilder();
        $qb->join('m.paymentProfile', 'pp');

        $qb->andWhere('m.nextBillingDate <= ?1');

//        if (isset($context['startDate'])) {
//            $startDate = new \DateTime($context['startDate']);
//            $query->filterGreater('nextBillingDate', $startDate);
//        }
        if (isset($context['paymentType'])) {
            switch($context['paymentType']) {
                case PaymentProfile::PAYMENT_PROFILE_TYPE_CREDIT_CARD:
                    $qb->andWhere('pp instance of \Vespolina\Entity\Partner\PaymentProfileType\CreditCard');
                    break;
                case PaymentProfile::PAYMENT_PROFILE_TYPE_INVOICE:
                    $qb->andWhere('pp instance of \Vespolina\Entity\Partner\PaymentProfileType\Invoice');
                    break;
            }
        }

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
            if (strstr($billingAgreement->getBillingInterval(), 'month') !== false) {
                foreach ($billingAgreement->getOrderItems() as $item) {
                    if (!$item->getAttribute('inactive')) {
                        $total += $item->getPricing()->getTotalValue();
                    }
                }
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

    /**
     * @return \Vespolina\Billing\Manager\BillingInvoiceManager
     */
    public function getBillingInvoiceManager()
    {
        return $this->billingInvoiceManager;
    }

    /**
     * @param \Vespolina\Billing\Manager\BillingInvoiceManager $billingInvoiceManager
     */
    public function setBillingInvoiceManager($billingInvoiceManager)
    {
        $this->billingInvoiceManager = $billingInvoiceManager;

        return $this;
    }

    /**
     * @return \ImmersiveLabs\CaraCore\Manager\UserManager
     */
    public function getUserManager()
    {
        return $this->userManager;
    }

    /**
     * @param \ImmersiveLabs\CaraCore\Manager\UserManager $userManager
     */
    public function setUserManager($userManager)
    {
        $this->userManager = $userManager;

        return $this;
    }

    /**
     * @return \ImmersiveLabs\BillingBundle\Service\BillingService
     */
    public function getBillingService()
    {
        return $this->billingService;
    }

    /**
     * @param \ImmersiveLabs\BillingBundle\Service\BillingService $billingService
     */
    public function setBillingService($billingService)
    {
        $this->billingService = $billingService;

        return $this;
    }

    /**
     * @return \Vespolina\Invoice\Manager\InvoiceManager
     */
    public function getInvoiceManager()
    {
        return $this->invoiceManager;
    }

    /**
     * @param \Vespolina\Invoice\Manager\InvoiceManager $invoiceManager
     */
    public function setInvoiceManager($invoiceManager)
    {
        $this->invoiceManager = $invoiceManager;

        return $this;
    }

    /**
     * Maps eligible licenses by the attached active billing agreement
     *
     * @param array $licenses
     * @return array
     */
    private function mapLicensesByBillingAgreements(array $licenses)
    {
        $map = array();

        /** @var License $license */
        foreach($licenses as $license) {
            if ($license->getItem()) {
                $item = $license->getItem();
                $ba = $item->getActiveBillingAgreement();

                $map[$ba->getId()][] = $license->getId();
            }
        }

        return $map;
    }
}
