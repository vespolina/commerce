<?php
/**
 * (c) Vespolina Project http://www.vespolina-project.org
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Vespolina\Order\Pricing;

use Vespolina\Order\Handler\CartHandlerInterface;
use Vespolina\Entity\Order\OrderInterface;
use Vespolina\Entity\Order\ItemInterface;
use Vespolina\Entity\Pricing\PricingContextInterface;

/**
 * @author Daniel Kucharski <daniel@xerias.be>
 * @author Richard Shank <develop@zestic.com>
 */
interface OrderPricingProviderInterface
{
    /**
     * Create a pricing set
     */
    function createPricingSet();

    /**
     * Add a cart handler for a product to the pricing provider
     *
     * @param \Vespolina\Order\Handler\OrderHandlerInterface $handler
     */
    function addOrderHandler(OrderHandlerInterface $handler);

    /**
     * Create a pricing context which holds 'global variables' used while computing prices
     *
     * @return
     */
    function createPricingContext();

    /**
     * Determine cart and (optionally) item level prices
     *
     * @param \Vespolina\Entity\Order\OrderInterface $cart
     * @param $pricingContext
     * @param $determineItemPrices
     */
    function determineOrderPrices(OrderInterface $cart, PricingContextInterface $pricingContext = null, $determineItemPrices = true);

    /**
     *
     *
     * @param \Vespolina\Order\Order\ItemInterface $cartItem
     * @param $pricingContext
     */
    function determineOrderItemPrices(ItemInterface $cartItem, PricingContextInterface $pricingContext);
}