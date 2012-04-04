<?php
/**
 * (c) 2012 Vespolina Project http://www.vespolina-project.org
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Vespolina\Invoice;

use Vespolina\Entity\OrderInterface;

/**
 * @author Richard Shank <develop@zestic.com>
 */
class InvoiceManager implements InvoiceManagerInterface
{
    private $invoiceClass;

    public function __construct($invoiceClass)
    {
        $this->invoiceClass = $invoiceClass;
    }

    public function createInvoice(OrderInterface $order)
    {
        $invoice = new $this->invoiceClass();
        $invoice->setOrder($order);

        return $invoice;
    }
}
