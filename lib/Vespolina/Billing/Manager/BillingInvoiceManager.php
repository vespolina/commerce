<?php

namespace Vespolina\Billing\Manager;

use Vespolina\Entity\Billing\BillingRequestInterface;
use Vespolina\Billing\Gateway\BillingGatewayInterface;
use Vespolina\EventDispatcher\EventDispatcherInterface;
use Molino\QueryInterface;
use ImmersiveLabs\DefaultBundle\Service\EmailService;
use Vespolina\Invoice\Manager\InvoiceManagerInterface;
use Vespolina\Entity\Billing\BillingRequest;
use Vespolina\Entity\Invoice\Invoice;
use Vespolina\Entity\Order\OrderEvents;
use ImmersiveLabs\CaraCore\Entity\User;
use Vespolina\Entity\Order\OrderInterface;
use Vespolina\Entity\Order\ItemInterface;
use ImmersiveLabs\Pricing\Entity\PricingSet;
use Vespolina\Entity\Partner\PartnerInterface;

class BillingInvoiceManager implements BillingInvoiceManagerInterface
{
    /** @var BillingGatewayInterface */
    protected $gateway;

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
     * @param BillingRequestInterface $invoice
     */
    public function sendNotification(User $user, BillingRequestInterface $invoice)
    {
        $this->emailService->sendInvoicePending($user, $invoice);
    }

    /**
     * @param PartnerInterface $partner
     * @param PricingSet $pricingSet
     * @return BillingRequestInterface
     */
    public function createInvoice(PartnerInterface $partner, PricingSet $pricingSet)
    {
        $amountDue = $pricingSet->get('totalValue');

        $invoice = new BillingRequest();
        $invoice
            ->setDueDate(new \DateTime($this->getDuration()))
            ->setPricingSet($pricingSet)
            ->setAmountDue($amountDue)
            ->setPartner($partner)
            ->setStatus(BillingRequest::STATUS_PENDING)
        ;
        $this->gateway->persistBillingRequest($invoice);

        return $invoice;
    }

    /**
     * @param BillingRequestInterface $invoice
     */
    public function tagAsPaid(BillingRequestInterface $invoice)
    {
        $partner = $invoice->getPartner();

        $logInvoice = new Invoice();
        $logInvoice
            ->setPayment($invoice->getAmountDue())
            ->setDueDate($invoice->getDueDate())
            ->setIssuedDate($invoice->getCreatedAt())
            ->setPartner($partner)
            ->setPeriodStart($invoice->getCreatedAt())
            ->setPeriodEnd(new \DateTime())
        ;

        $this->getInvoiceManager()->updateInvoice($logInvoice);

        $invoice
            ->setInvoice($logInvoice)
            ->setStatus(BillingRequest::STATUS_PAID)
        ;

        $this->gateway->updateBillingRequest($invoice);
    }

    /**
     * @return \Vespolina\Billing\Gateway\BillingGatewayInterface
     */
    public function getGateway()
    {
        return $this->gateway;
    }

    /**
     * @param \Vespolina\Billing\Gateway\BillingGatewayInterface $gateway
     */
    public function setGateway($gateway)
    {
        $this->gateway = $gateway;

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
