<?php

/**
 * (c) 2011 - ∞ Vespolina Project http://www.vespolina-project.org
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Vespolina\Order\Handler;

use Vespolina\Order\Handler\CartHandlerInterface;
use Vespolina\Entity\Order\ItemInterface;
use Vespolina\Entity\Pricing\PricingSet;

/**
 * This provides a default set of actions for the methods that can be used by any other CartHandler by extending this class
 */
abstract class AbstractOrderHandler implements OrderHandlerInterface
{
    protected $taxationManager;

    public function createPricingSet()
    {
        return new PricingSet();
    }

    public function determineCartItemPrices(ItemInterface $cartItem, $pricingContext)
    {
        throw new \Exception("Sorry determineOrderItemPrices() doesn't have default functionality in place");
    }

    public function setTaxationManager($taxationManager)
    {
        $this->taxationManager = $taxationManager;
    }
}
