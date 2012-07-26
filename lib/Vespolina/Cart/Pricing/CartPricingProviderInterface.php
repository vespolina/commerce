<?php
/**
 * (c) Vespolina Project http://www.vespolina-project.org
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Vespolina\Cart\Pricing;

use Vespolina\Cart\Handler\CartHandlerInterface;
use Vespolina\Entity\Order\CartInterface;
use Vespolina\Entity\Order\ItemInterface;
use Vespolina\Entity\Pricing\PricingContextInterface;

/**
 * @author Daniel Kucharski <daniel@xerias.be>
 * @author Richard Shank <develop@zestic.com>
 */
interface CartPricingProviderInterface
{
    /**
     * Create a pricing set
     */
    function createPricingSet();

    /**
     * Add a cart handler for a product to the pricing provider
     *
     * @param \Vespolina\Cart\Handler\CartHandlerInterface $handler
     */
    function addCartHandler(CartHandlerInterface $handler);

    /**
     * Create a pricing context which holds 'global variables' used while computing prices
     *
     * @return
     */
    function createPricingContext();

    /**
     * Determine cart and (optionally) item level prices
     *
     * @param \Vespolina\Entity\Order\CartInterface $cart
     * @param $pricingContext
     * @param $determineItemPrices
     */
    function determineCartPrices(CartInterface $cart, PricingContextInterface $pricingContext = null, $determineItemPrices = true);

    /**
     *
     *
     * @param \Vespolina\Cart\Order\ItemInterface $cartItem
     * @param $pricingContext
     */
    function determineCartItemPrices(ItemInterface $cartItem, PricingContextInterface $pricingContext);
}