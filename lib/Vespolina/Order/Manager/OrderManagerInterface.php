<?php
/**
 * (c) 2012 Vespolina Project http://www.vespolina-project.org
 *
 * (c) Daniel Kucharski <daniel@xerias.be>
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Vespolina\Order\Manager;

use Vespolina\Entity\Order\OrderInterface;
use Vespolina\Entity\Order\ItemInterface;
use Vespolina\Entity\Product\ProductInterface;
use Vespolina\Entity\Pricing\PricingSetInterface;

interface OrderManagerInterface
{
    /**
     * Add a product to the cart.
     * This also triggers a OrderEvents::INIT_ITEM event
     *
     * @param \Vespolina\Entity\Order\OrderInterface $order
     * @param \Vespolina\Entity\ProductInterface $product
     * @param integer $quantity - null defaults to one item
     *
     * @returns \Vespolina\Entity\Order\ItemInterface
     */
    function addProductToOrder(OrderInterface $order, ProductInterface $product, array $options = null, $orderedQuantity = null);

    /**
     * Create a new cart instance.
     * This also triggers a OrderEvents::INIT_CART event
     *
     * @param string $name Name of the cart
     *
     * @return \Vespolina\Entity\Order\OrderInterface
     */
    function createOrder($name = 'default');

    /**
     * Find a cart by the specified fields and values
     *
     * @param array $criteria
     * @param array $orderBy
     * @param null $limit
     * @param null $offset
     *
     * @return array|\Vespolina\Entity\Order\OrderInterface|null
     */
    function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null);

    /**
     * Find a cart by the system id
     *
     * @param $id
     * @return \Vespolina\Entity\Order\OrderInterface|null
     */
    function findOrderById($id);

    /**
     * Find a cart item that contains the passed product
     *
     * @param \Vespolina\Entity\Order\OrderInterface $cart
     * @param \Vespolina\Entity\ProductInterface $product
     * @param array $options
     *
     * @return \Vespolina\Entity\Order\ItemInterface|null
     */
    function findProductInOrder(OrderInterface $cart, ProductInterface $product, array $options = null);

    /**
     * Return the EventDistpatcher for this manager
     *
     * @return \Vespolina\EventDispatcher\EventDispatcherInterface
     */
    function getEventDispatcher();

    /**
     * Completely remove a product from the cart
     * This also triggers a OrderEvents::REMOVE_ITEM event
     *
     * @param \Vespolina\Entity\Order\OrderInterface $cart
     * @param \Vespolina\Entity\ProductInterface $product
     * @param array $options
     * @param bool $andPersist
     */
    function removeProductFromOrder(OrderInterface $cart, ProductInterface $product, array $options = null, $andPersist = true);

    /**
     * Manually set the state of an item in the cart
     * This also triggers an OrderEvents::UPDATE_ITEM_STATE event
     *
     * @param \Vespolina\Entity\Order\ItemInterface $cartItem
     * @param $state
     */
    function setOrderItemState(ItemInterface $cartItem, $state);

    /**
     * Manually set the pricing set for a cart
     *
     * @param \Vespolina\Entity\Order\OrderInterface $cart
     * @param \Vespolina\Entity\Pricing\PricingSetInterface $pricingSet
     */
//    function setOrderPricingSet(OrderInterface $cart, PricingSetInterface $pricingSet);

    /**
     * Manually set the state of the cart.
     * This also triggers an OrderEvents::UPDATE_CART_STATE event
     *
     * @param \Vespolina\Entity\Order\OrderInterface $cart
     * @param $state
     */
    function setOrderState(OrderInterface $cart, $state);

    /**
     * Set the quantity for an item.
     * This also triggers an OrderEvents::UPDATE_ITEM event
     *
     * @param \Vespolina\Entity\Order\ItemInterface $cart
     * @param integer $quantity
     */
    function setItemQuantity(ItemInterface $item, $quantity);

    /**
     * Find the product in the cart and set the quantity for it
     * This also triggers an OrderEvents::UPDATE_ITEM event
     *
     * @param \Vespolina\Entity\Order\OrderInterface $cart
     * @param \Vespolina\Entity\ProductInterface $product
     * @param array $options
     * @param integer $quantity
     */
    function setProductQuantity(OrderInterface $cart, ProductInterface $product, array $options, $quantity);

    /**
     * Triggers a OrderEvents::UPDATE_CART event and by default, persists the cart
     *
     * @param \Vespolina\Entity\Order\OrderInterface $cart
     * @param boolean $andPersist defaults to true
     */
    function updateOrder(OrderInterface $cart, $andPersist = true);
}