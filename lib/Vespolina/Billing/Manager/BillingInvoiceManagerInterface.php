<?php

namespace Vespolina\Billing\Manager;

use Molino\QueryInterface;
use Vespolina\Entity\Billing\BillingRequestInterface;
use Vespolina\Entity\Order\OrderInterface;
use Vespolina\Entity\Partner\PartnerInterface;

interface BillingInvoiceManagerInterface
{

    /**
     * @return QueryInterface
     */
    function createSelectQuery();

    /**
     * @param $status
     * @return array
     */
    function findAllByStatus($status);

    /**
     * @param BillingRequestInterface $invoice
     */
    function sendNotification(User $user, BillingRequestInterface $invoice);

    /**
     * @param PartnerInterface $partner
     * @param PricingSet $pricingSet
     * @param $orderItems
     * @return BillingRequestInterface
     */
    function createInvoice(PartnerInterface $partner, PricingSet $pricingSet, $orderItems);

    /**
     * @param BillingRequestInterface $invoice
     */
    function tagAsPaid(BillingRequestInterface $invoice);

    /**
     * @return \Vespolina\Billing\Gateway\BillingGatewayInterface
     */
    function getGateway();

    /**
     * @param \Vespolina\Billing\Gateway\BillingGatewayInterface $gateway
     */
    function setGateway($gateway);
    /**
     * @param string $duration
     * @return BillingInvoiceManagerInterface
     */
    function setDuration($duration);

    /**
     * @return string
     */
    function getDuration();
}
