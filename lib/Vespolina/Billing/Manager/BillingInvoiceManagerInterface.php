<?php

namespace Vespolina\Billing\Manager;

use Vespolina\Entity\Billing\BillingRequestInterface;
use ImmersiveLabs\Pricing\Entity\PricingSet;
use Vespolina\Entity\Partner\PartnerInterface;
use Molino\QueryInterface;
use ImmersiveLabs\CaraCore\Entity\User;
use Vespolina\Entity\Order\OrderInterface;

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
     * @return \ImmersiveLabs\DefaultBundle\Service\EmailService
     */
    function getEmailService();

    /**
     * @param \ImmersiveLabs\DefaultBundle\Service\EmailService $emailService
     */
    function setEmailService($emailService);

    /**
     * @return \Vespolina\Invoice\Manager\InvoiceManagerInterface
     */
    function getInvoiceManager();

    /**
     * @param \Vespolina\Invoice\Manager\InvoiceManagerInterface $invoiceManager
     */
    function setInvoiceManager($invoiceManager);

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
