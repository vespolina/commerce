<?php
/**
 * (c) 2012 Vespolina Project http://www.vespolina-project.org
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Vespolina\Cart\Handler;

use Vespolina\Cart\Handler\CartHandlerInterface;
use Vespolina\Entity\ItemInterface;
use Vespolina\Cart\Pricing\PricingSet;

/**
 * This provides a default set of actions for the methods that can be used by any other CartHandler by extending this class
 */
abstract class AbstractCartHandler implements CartHandlerInterface
{
    protected $taxationManager;

    public function createPricingSet()
    {
        return new PricingSet();
    }

    public function determineCartItemPrices(ItemInterface $cartItem, $pricingContext)
    {
        throw new \Exception("Sorry determineCartItemPrices() doesn't have default functionality in place");
    }

    public function setTaxationManager($taxationManager)
    {
        $this->taxationManager = $taxationManager;
    }
}
