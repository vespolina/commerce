<?php
/**
 * (c) 2013 Vespolina Project http://www.vespolina-project.org
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Vespolina\Billing\Manager;

use Vespolina\Entity\Order\OrderInterface;
use Vespolina\Entity\Partner\PartnerInterface;

class BillingManager implements BillingManagerInterface
{
    public function processOrder($order)
    {
        // find current bill

    }

    public function createBillingAgreement(OrderInterface $order)
    {

    }

    function createBillingRequest(PartnerInterface $partner)
    {

    }
}
