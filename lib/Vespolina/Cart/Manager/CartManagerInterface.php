<?php
/**
 * (c) 2012 Vespolina Project http://www.vespolina-project.org
 *
 * (c) Daniel Kucharski <daniel@xerias.be>
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Vespolina\Cart\Manager;

use Vespolina\Entity\Order\CartInterface;
use Vespolina\Entity\Order\ItemInterface;
use Vespolina\Entity\ProductInterface;
use Vespolina\Cart\Pricing\PricingSetInterface;

interface CartManagerInterface
{
    /**
     * Add a product to the cart.
     * This also triggers a CartEvents::INIT_ITEM event
     *
     * @param Vespolina\Entity\Order\CartInterface $cart
     * @param Vespolina\Entity\ProductInterface $product
     * @param integer $quantity - null defaults to one item
     *
     * @returns Vespolina\Entity\Order\ItemInterface
     */
    function addProductToCart(CartInterface $cart, ProductInterface $product, array $options = null, $orderedQuantity = null);

    /**
     * Create a new cart instance.
     * This also triggers a CartEvents::INIT event
     *
     * @param string $name Name of the cart
     *
     * @return Vespolina\Entity\Order\CartInterface
     */
    function createCart($name = 'default');

    /**
     * Calculate prices for a given cart.
     * This also triggers a
     *
     * @param CartInterface $cart
     * @param bool $determineItemPrices
     */
    function determinePrices(CartInterface $cart, $determineItemPrices = true);

    /**
     * Find a cart by the specified fields and values
     *
     * @param array $criteria
     * @param array $orderBy
     * @param null $limit
     * @param null $offset
     *
     * @return array|Vespolina\Entity\Order\CartInterface|null
     */
    function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null);

    /**
     * Find a cart by the system id
     *
     * @param $id
     * @return Vespolina\Entity\Order\CartInterface|null
     */
    function findCartById($id);

    /**
     * Find a cart item that contains the passed product
     *
     * @param \Vespolina\Entity\Order\CartInterface $cart
     * @param \Vespolina\Entity\ProductInterface $product
     *
     * @return Vespolina\Entity\Order\ItemInterface|null
     */
    function findProductInCart(CartInterface $cart, ProductInterface $product);

    /**
     * Return the EventDistpatcher for this manager
     * 
     * @return Vespolina\EventDispatcher\EventDispatcherInterface
     */
    function getEventDispatcher();

    /**
     * Return the PricingProvider for this manager
     *
     * @return Vespolina\Entity\Pricing\PricingProviderInterface
     */
    function getPricingProvider();

    /**
     * Completely remove a product from the cart
     *
     * @param Vespolina\Entity\Order\CartInterface $cart
     * @param Vespolina\Entity\ProductInterface $product
     * @param bool $andPersist
     */
    function removeProductFromCart(CartInterface $cart, ProductInterface $product, $andPersist = true);

    /**
     * Manually set the state of an item in the cart
     * This also triggers an CartEvents::ITEM_CHANGE event
     *
     * @param Vespolina\Entity\Order\ItemInterface $cartItem
     * @param $state
     */
    function setCartItemState(ItemInterface $cartItem, $state);

    /**
     * Manually set the pricing set for a cart
     *
     * @param Vespolina\Entity\Order\CartInterface $cart
     * @param Vespolina\Entity\Pricing\PricingSetInterface $pricingSet
     */
    function setCartPricingSet(CartInterface $cart, PricingSetInterface $pricingSet);

    /**
     * Manually set the state of the cart.
     * This also triggers an CartEvents::STATE_CHANGE event
     *
     * @param Vespolina\Entity\Order\CartInterface $cart
     * @param $state
     */
    function setCartState(CartInterface $cart, $state);

    /**
     * Find the product in the cart and set the quantity for it
     * This also triggers an CartEvents::ITEM_CHANGE event
     *
     * @param Vespolina\Entity\Order\CartInterface $cart
     * @param Vespolina\Entity\ProductInterface $product
     * @param integer $quantity
     */
    function setProductQuantity(CartInterface $cart, ProductInterface $product, $quantity);

    /**
     * Triggers a CartEvents::UPDATE event and by default, persists the cart
     *
     * @param Vespolina\Entity\Order\CartInterface $cart
     * @param boolean $andPersist defaults to true
     */
    function updateCart(CartInterface $cart, $andPersist = true);
}