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
interface InvoiceManagerInterface
{
    /**
     * Create a new invoice from a customer order
     *
     * @param \Vespolina\Entity\OrderInterface $order
     *
     * @return Vespolina\Entity\InvoiceInterface;
     */
    function createInvoice(OrderInterface $order);
}
