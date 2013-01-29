<?php

namespace Vespolina\Billing\Manager;

use Vespolina\Entity\Billing\BillingInvoiceInterface;
use Vespolina\Billing\Gateway\BillingInvoiceGatewayInterface;
use Vespolina\EventDispatcher\EventDispatcherInterface;
use Molino\QueryInterface;
use ImmersiveLabs\DefaultBundle\Service\EmailService;
use Vespolina\Invoice\Manager\InvoiceManagerInterface;
use Vespolina\Entity\Billing\BillingInvoice;
use Vespolina\Entity\Invoice\Invoice;
use Vespolina\Entity\Order\OrderEvents;
use ImmersiveLabs\CaraCore\Entity\User;
use Vespolina\Entity\Order\OrderInterface;
use Vespolina\Entity\Order\ItemInterface;

class BillingInvoiceManager implements BillingInvoiceManagerInterface
{
    /** @var BillingInvoiceGatewayInterface */
    protected $gateway;

    /** @var EventDispatcherInterface */
    protected $eventDispatcher;

    /** @var EmailService */
    protected $emailService;

    /** @var InvoiceManagerInterface */
    protected $invoiceManager;

    /** @var string */
    protected $duration;

    /**
     * @param $status
     * @return array
     */
    public function findAllByStatus($status)
    {
        return $this->createSelectQuery()
            ->filterEqual('status', $status)
            ->all();
    }

    /**
     * @param BillingInvoiceInterface $invoice
     */
    public function sendNotification(User $user, BillingInvoiceInterface $invoice)
    {
        $this->emailService->sendInvoicePaid($user, $invoice);
    }

    /**
     * @param OrderInterface $order
     * @return BillingInvoiceInterface
     */
    public function createInvoice(OrderInterface $order)
    {
        $amountDue = 0;

        foreach ($order->getItems() as $item) {
            /** @var ItemInterface $item */

            $amountDue += $item->getPricing()->getTotalValue();
        }

        $invoice = new BillingInvoice();
        $invoice
            ->setDueDate(new \DateTime($this->getDuration()))
            ->setOrder($order)
            ->setAmountDue($amountDue)
            ->setStatus(BillingInvoice::STATUS_PENDING)
        ;
        $this->gateway->persistBillingInvoice($invoice);

        return $invoice;
    }

    /**
     * @param BillingInvoiceInterface $invoice
     */
    public function tagAsPaid(BillingInvoiceInterface $invoice)
    {
        $partner = $invoice->getOrder()->getPartner();

        $logInvoice = new Invoice();
        $logInvoice
            ->setPayment($invoice->getAmountDue())
            ->setDueDate($invoice->getDueDate())
            ->setIssuedDate($invoice->getCreatedAt())
            ->setPartner($partner)
            ->setPeriodStart($invoice->getCreatedAt())
            ->setPeriodEnd(new \DateTime())
        ;

        $logInvoice->getOrders()->add($invoice->getOrder());

        $this->getInvoiceManager()->updateInvoice($logInvoice);

        $invoice
            ->setInvoice($logInvoice)
            ->setStatus(BillingInvoice::STATUS_PAID)
        ;

        $this->gateway->updateBillingInvoice($invoice);

        $event = $this->eventDispatcher->createEvent($invoice->getOrder());
        $this->eventDispatcher->dispatch(OrderEvents::FINISHED, $event);
    }

    /**
     * @return \Vespolina\Billing\Gateway\BillingInvoiceGatewayInterface
     */
    public function getGateway()
    {
        return $this->gateway;
    }

    /**
     * @param \Vespolina\Billing\Gateway\BillingInvoiceGatewayInterface $gateway
     */
    public function setGateway($gateway)
    {
        $this->gateway = $gateway;

        return $this;
    }

    /**
     * @return \Vespolina\EventDispatcher\EventDispatcherInterface
     */
    public function getEventDispatcher()
    {
        return $this->eventDispatcher;
    }

    /**
     * @param \Vespolina\EventDispatcher\EventDispatcherInterface $eventDispatcher
     */
    public function setEventDispatcher($eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;

        return $this;
    }

    /**
     * @return QueryInterface
     */
    public function createSelectQuery()
    {
        return $this->gateway->createQuery('select');
    }

    /**
     * @return \ImmersiveLabs\DefaultBundle\Service\EmailService
     */
    public function getEmailService()
    {
        return $this->emailService;
    }

    /**
     * @param \ImmersiveLabs\DefaultBundle\Service\EmailService $emailService
     */
    public function setEmailService($emailService)
    {
        $this->emailService = $emailService;

        return $this;
    }

    /**
     * @return \Vespolina\Invoice\Manager\InvoiceManagerInterface
     */
    public function getInvoiceManager()
    {
        return $this->invoiceManager;
    }

    /**
     * @param \Vespolina\Invoice\Manager\InvoiceManagerInterface $invoiceManager
     */
    public function setInvoiceManager($invoiceManager)
    {
        $this->invoiceManager = $invoiceManager;

        return $this;
    }

    /**
     * @return string
     */
    public function getDuration()
    {
        return $this->duration;
    }

    /**
     * @param string $duration
     */
    public function setDuration($duration)
    {
        $this->duration = $duration;

        return $this;
    }
}
