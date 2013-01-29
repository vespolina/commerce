<?php

namespace Vespolina\Billing\Gateway;

use Vespolina\Entity\Billing\BillingInvoiceInterface;
use Molino\MolinoInterface;

class BillingInvoiceGateway implements BillingInvoiceGatewayInterface
{
    protected $molino;
    protected $billingInvoiceClass;

    /**
     * @param \Molino\MolinoInterface $molino
     * @param string $managedClass
     */
    public function __construct(MolinoInterface $molino, $billingInvoiceClass)
    {
        if (!class_exists($billingInvoiceClass) ||
            !in_array('Vespolina\Entity\Billing\BillingInvoiceInterface', class_implements($billingInvoiceClass))) {
            throw new InvalidInterfaceException('Please have your billingInvoice class implement Vespolina\Entity\Billing\BillingInvoiceInterface');
        }
        $this->molino = $molino;
        $this->billingInvoiceClass = $billingInvoiceClass;
    }

    /**
     * @param $type
     * @param null $queryClass
     * @return mixed
     */
    public function createQuery($type, $queryClass = null)
    {
        $type = ucfirst(strtolower($type));
        if (!in_array($type, array('Delete', 'Select', 'Update'))) {
            throw new InvalidArgumentException($type . ' is not a valid Query type');
        }
        $queryFunction = 'create' . $type . 'Query';

        if (!$queryClass) {
            $queryClass = $this->billingInvoiceClass;
        }
        return $this->molino->$queryFunction($queryClass);
    }

    /**
     * @param BillingInvoiceInterface $invoice
     */
    public function persistBillingInvoice(BillingInvoiceInterface $invoice)
    {
        $this->molino->save($invoice);
    }

    /**
     * @param BillingInvoiceInterface $invoice
     */
    public function updateBillingInvoice(BillingInvoiceInterface $invoice)
    {
        $this->molino->save($invoice);
    }

    /**
     * @param BillingInvoiceInterface $invoice
     */
    public function deleteBillingInvoice(BillingInvoiceInterface $invoice)
    {
        $this->molino->delete($invoice);
    }
}
