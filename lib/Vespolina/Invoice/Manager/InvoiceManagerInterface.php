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

interface InvoiceManagerInterface
{
    /**
     * Create a new invoice from a customer order
     */
    function createInvoice();

    /**
     * @param $id
     * @return \Vespolina\Entity\Invoice\InvoiceInterface
     */
    function findById($id);

    /**
     * @param \Vespolina\Entity\Partner\Partner $partner
     * @return array
     */
    function findAllInvoicesByPartner(PartnerInterface $partner);

    /**
     * @param \Vespolina\Entity\Partner\Partner $partner
     * @param \DateTime $periodStart
     * @param \DateTime $periodEnd
     * @return array
     */
    function findInvoicesByPartnerAndPeriod($partner, $periodStart, $periodEnd);
}
