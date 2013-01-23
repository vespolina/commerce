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
    public function processOrder(OrderInterface $order)
    {
        // order should already be processed, todo: add a flag to make sure?
        $items = $order->getItems();
        foreach ($items as $item) {

        }
     }


    public function createBillingAgreement(OrderInterface $order)
    {

    }

    function createBillingRequest(PartnerInterface $partner)
    {

    }
}
