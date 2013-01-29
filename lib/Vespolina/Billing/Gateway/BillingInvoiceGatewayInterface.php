<?php

namespace Vespolina\Billing\Gateway;
use Vespolina\Entity\Billing\BillingInvoiceInterface;

interface BillingInvoiceGatewayInterface
{
    /**
     * @param $type
     * @param null $queryClass
     * @return mixed
     */
    function createQuery($type, $queryClass = null);

    /**
     * @param BillingInvoiceInterface $invoice
     */
    function persistBillingInvoice(BillingInvoiceInterface $invoice);

    /**
     * @param BillingInvoiceInterface $invoice
     */
    function updateBillingInvoice(BillingInvoiceInterface $invoice);

    /**
     * @param BillingInvoiceInterface $invoice
     */
    function deleteBillingInvoice(BillingInvoiceInterface $invoice);
}
