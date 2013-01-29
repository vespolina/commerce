<?php

namespace Vespolina\Billing\Manager;

use Vespolina\Entity\Billing\BillingInvoiceInterface;
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
     * @param BillingInvoiceInterface $invoice
     */
    function sendNotification(User $user, BillingInvoiceInterface $invoice);

    /**
     * @param OrderInterface $order
     * @return BillingInvoiceInterface
     */
    function createInvoice(OrderInterface $order);

    /**
     * @param BillingInvoiceInterface $invoice
     */
    function tagAsPaid(BillingInvoiceInterface $invoice);

    /**
     * @return \Vespolina\Billing\Gateway\BillingInvoiceGatewayInterface
     */
    function getGateway();

    /**
     * @param \Vespolina\Billing\Gateway\BillingInvoiceGatewayInterface $gateway
     */
    function setGateway($gateway);

    /**
     * @return \Vespolina\EventDispatcher\EventDispatcherInterface
     */
    function getEventDispatcher();

    /**
     * @param \Vespolina\EventDispatcher\EventDispatcherInterface $eventDispatcher
     */
    function setEventDispatcher($eventDispatcher);

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
