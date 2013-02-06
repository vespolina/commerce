<?php
/**
 * (c) 2012 Vespolina Project http://www.vespolina-project.org
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Vespolina\Invoice\Manager;

/**
 * @author Richard Shank <develop@zestic.com>
 */
use Vespolina\Entity\Partner\PartnerInterface;
use Vespolina\Entity\Invoice\InvoiceInterface;

interface InvoiceManagerInterface
{
    /**
     * Create a new invoice from a customer order
     * @return \Vespolina\Entity\Invoice\InvoiceInterface
     */
    function createInvoice();

    /**
     * @param $id
     * @return \Vespolina\Entity\Invoice\InvoiceInterface
     */
    function findById($id);

    function findAll();

    /**
     * @param \Vespolina\Entity\Partner\Partner $partner
     * @return array
     */
    function findAllInvoicesByPartner(PartnerInterface $partner);

    /**
     * @param \Vespolina\Entity\Partner\Partner $partner
     * @return InvoiceInterface
     */
    function findLastInvoiceByPartner(PartnerInterface $partner);

    /**
     * @param \Vespolina\Entity\Partner\Partner $partner
     * @param \DateTime $periodStart
     * @param \DateTime $periodEnd
     * @return array
     */
    function findInvoicesByPartnerAndPeriod(PartnerInterface $partner, $periodStart, $periodEnd);

    /**
     * Update and persist the invoice
     *
     * @param \Vespolina\Entity\Invoice\InvoiceInterface $invoice
     * @param Boolean $andFlush Whether to flush the changes (default true)
     */
    function updateInvoice(InvoiceInterface $invoice, $andPersist = true);
}
