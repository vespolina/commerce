<?php
/**
 * (c) 2013 Vespolina Project http://www.vespolina-project.org
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Vespolina\Billing\Manager;

use Vespolina\Entity\Billing\BillingAgreement;
use Vespolina\Entity\Order\OrderInterface;
use Vespolina\Entity\Partner\PartnerInterface;
use Vespolina\Order\Manager\OrderManager;
use Vespolina\Billing\Gateway\BillingAgreementGateway;

class BillingManager implements BillingManagerInterface
{
    protected $gateway;
    protected $orderManager;

    public function __construct(BillingAgreementGatewayInterface $gateway, OrderManager $orderManager)
    {
        $this->gateway = $gateway;
        $this->orderManager = $orderManager;
    }

    /**
     * @param \Vespolina\Entity\Order\OrderInterface $order
     * @return boolean
     */
    public function processOrder(OrderInterface $order)
    {
        $billingAgreements = $this->createBillingAgreements($order);

        // order already processed
        if (count($billingAgreements) == 0) {
            return false;
        }

        // process further

        return true;
     }

    /**
     * @param \Vespolina\Entity\Order\OrderInterface $order
     * @return array
     */
    public function createBillingAgreements(OrderInterface $order)
    {
        /** @var $item */
        foreach ($order->getItems() as $item) {

            $pricingSet = $item->getPricing();
            if ($pricingSet->get)
            $startDate = new \DateTime('now');

            $billingAgreement = new BillingAgreement();
            $billingAgreement
                ->setPaymentGateway($order->getAttribute('payment_gateway'))
                ->setPartner($order->getOwner())
                ->setInitialBillingDate(new \DateTime('now'))
                ->setBillingAmount()
                ->setOrderItem($item)
            ;
        }

        return array();
    }

    /**
     * @param \Vespolina\Entity\Partner\PartnerInterface $partner
     * @return \Vespolina\Entity\Billing\BillingRequestInterface|void
     */
    function createBillingRequest(PartnerInterface $partner)
    {
        $orderArray = $this->orderManager->findClosedOrdersByOwner($partner);
        //$orderArray->toArray();
    }
}
