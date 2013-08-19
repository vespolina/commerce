<?php

/**
 * (c) 2011 - ∞ Vespolina Project http://www.vespolina-project.org
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Vespolina\Invoice\Manager;

use Molino\Doctrine\ORM\SelectQuery;
use Vespolina\Entity\Billing\BillingRequest;
use Vespolina\Entity\Invoice\InvoiceInterface;
use Vespolina\Entity\Partner\PartnerInterface;
use Vespolina\Invoice\Gateway\InvoiceGateway;

/**
 * @author Richard Shank <develop@zestic.com>
 */
class InvoiceManager implements InvoiceManagerInterface
{
    protected $gateway;

    public function __construct(InvoiceGateway $gateway, $classMapping)
    {
        $missingClasses = array();
        foreach (array('invoice', 'item') as $class) {
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
            throw new InvalidConfigurationException(sprintf("The following invoice classes are missing from configuration: %s", join(', ', $missingClasses)));
        }

        $this->gateway = $gateway;
    }

    /**
     * @inheritdoc
     */
    public function createInvoice($type = 'default')
    {
        $invoice = new $this->invoiceClass();

        return $invoice;
    }

    /**
     * @inheritdoc
     */
    public function createInvoiceItem()
    {
        return new $this->itemClass();

        return $invoice;
    }

    /**
     * @inheritdoc
     */
    public function findById($id)
    {
        return $this->gateway->createQuery('Select')
            ->filterEqual('id', $id)
            ->one()
        ;
    }

    /**
     * @inheritdoc
     */
    public function findAll()
    {
        $query = $this->gateway->createQuery('Select');
        return $query->sort('paid', 'asc')
            ->all()
            ;
    }

    /**
     * @inheritdoc
     */
    public function findAllInvoicesByPartner(PartnerInterface $partner)
    {
        $query = $this->gateway->createQuery('Select')
            ->filterEqual('partner', $partner)
            ->sort('periodEnd', 'desc');

        return $query->all();
    }

    /**
     * @param PartnerInterface $partner
     * @param null $interval
     * @return mixed
     */
    public function findAllInvoicesByPartnerUsingInterval(PartnerInterface $partner, $interval = null)
    {
        /** @var $query SelectQuery */
        $query = $this->gateway->createQuery('Select');

        $query
            ->filterEqual('partner', $partner)
            ->sort('periodEnd', 'desc')
        ;

        if (!empty($interval)) {
            $query->filterGreaterEqual('issuedDate', new \DateTime($interval));
        }

        return $query->all();
    }

    /**
     * @inheritdoc
     */
    public function findLastInvoiceByPartner(PartnerInterface $partner)
    {
        $query = $this->gateway->createQuery('Select');
        return $query->filterEqual('partner', $partner)
            ->sort('periodEnd', 'desc')
            ->one()
            ;
    }

    /**
     * @inheritdoc
     */
    public function findInvoicesByPartnerAndPeriod(PartnerInterface $partner, $periodStart, $periodEnd)
    {
        $query = $this->gateway->createQuery('Select');
        return $query->filterEqual('partner', $partner)
            ->filterEqual('periodStart', $periodStart)
            ->filterEqual('periodEnd', $periodEnd)
            ->all()
        ;
    }

    /**
     * @inheritdoc
     */
    public function updateInvoice(InvoiceInterface $invoice, $andPersist = true)
    {
        $this->gateway->updateInvoice($invoice);
    }

    public function createInvoiceFromBillingRequest(BillingRequest $billingRequest)
    {
        $invoice = $this->createInvoice();
        $invoice->setDueDate($billingRequest->getDueDate());
        $invoice->setIssuedDate(new \DateTime());
        $invoice->setPeriodStart($billingRequest->getPeriodStart());
        $invoice->setPeriodEnd($billingRequest->getPeriodEnd());
        $invoice->setPayment($billingRequest->getAmountDue());
        $invoice->setPartner($billingRequest->getPartner());

        return $invoice;
    }

    public function persistInvoice(InvoiceInterface $invoice)
    {
        $this->gateway->persistInvoice($invoice);
    }
}
