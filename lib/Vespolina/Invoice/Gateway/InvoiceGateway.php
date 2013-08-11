<?php

namespace Vespolina\Invoice\Gateway;

use Molino\MolinoInterface;
use Molino\SelectQueryInterface;
use Vespolina\Entity\Invoice\InvoiceInterface;
use Vespolina\Exception\InvalidInterfaceException;

class InvoiceGateway
{
    protected $molino;
    protected $invoiceClass;

    /**
     * @param \Molino\MolinoInterface $molino
     * @param string $managedClass
     */
    public function __construct(MolinoInterface $molino, $invoiceClass)
    {
        if (!class_exists($invoiceClass) || !in_array('Vespolina\Entity\Invoice\InvoiceInterface', class_implements($invoiceClass))) {
            throw new InvalidInterfaceException('Please have your invoice class implement Vespolina\Entity\Invoice\InvoiceInterface');
        }
        $this->molino = $molino;
        $this->invoiceClass = $invoiceClass;
    }

    /**
     * @param string $type
     * @param type $queryClass
     * @return type
     * @throws InvalidArgumentException
     */
    public function createQuery($type, $queryClass = null)
    {
        $type = ucfirst(strtolower($type));
        if (!in_array($type, array('Delete', 'Select', 'Update'))) {
            throw new InvalidArgumentException($type . ' is not a valid Query type');
        }
        $queryFunction = 'create' . $type . 'Query';

        if (!$queryClass) {
            $queryClass = $this->invoiceClass;
        }
        return $this->molino->$queryFunction($queryClass);
    }

    /**
     * @param \Vespolina\Entity\Invoice\InvoiceInterface $invoice
     */
    public function deleteInvoice(InvoiceInterface $invoice)
    {
        $this->molino->delete($invoice);
    }

    /**
     * @param \Molino\SelectQueryInterface $query
     * @return \Vespolina\Entity\Invoice\InvoiceInterface
     */
    public function findInvoice(SelectQueryInterface $query)
    {
        return $query->one();
    }

    /**
     * @param \Molino\SelectQueryInterface $query
     * @return \Vespolina\Entity\Invoice\InvoiceInterface
     */
    public function findInvoices(SelectQueryInterface $query)
    {
        return $query->all();
    }

    /**
     * @param \Vespolina\Entity\Invoice\InvoiceInterface $invoice
     */
    public function persistInvoice(InvoiceInterface $invoice)
    {
        $this->molino->save($invoice);
    }

    /**
     * @param \Vespolina\Entity\Invoice\InvoiceInterface $invoice
     */
    public function updateInvoice(InvoiceInterface $invoice)
    {
        $this->molino->save($invoice);
    }
}
