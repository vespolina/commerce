<?php
/**
 * (c) 2012 Vespolina Project http://www.vespolina-project.org
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Vespolina\Order\Handler;

use Vespolina\Entity\Order\ItemInterface;

interface OrderHandlerInterface
{
    function createPricingSet();

    function determineOrderItemPrices(ItemInterface $cartItem, $pricingContext);

    function getTypes();
}
