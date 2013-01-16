<?php
/**
 * (c) 2011-2012 Vespolina Project http://www.vespolina-project.org
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Vespolina\Order\Manager;

use Doctrine\ORM\QueryBuilder;
use Gateway\Query;
use Vespolina\Entity\Order\CartEvents;
use Vespolina\Order\Gateway\OrderGatewayInterface;
use Vespolina\Order\Manager\OrderManagerInterface;
use Vespolina\Entity\Pricing\PricingSetInterface;
use Vespolina\Entity\Order\Order;
use Vespolina\Entity\Order\OrderInterface;
use Vespolina\Entity\Order\ItemInterface;
use Vespolina\Entity\Product\ProductInterface;
use Vespolina\EventDispatcher\EventDispatcherInterface;
use Vespolina\EventDispatcher\NullDispatcher;

/**
 * @author Daniel Kucharski <daniel@xerias.be>
 * @author Richard Shank <develop@zestic.com>
 */
class OrderManager implements OrderManagerInterface
{
    protected $cartClass;
    protected $cartItemClass;
    protected $eventDispatcher;
    protected $gateway;

    function __construct(OrderGatewayInterface $gateway, $cartClass, $cartItemClass, $cartEvents, EventDispatcherInterface $eventDispatcher = null)
    {
        if (!$eventDispatcher) {
            $eventDispatcher = new NullDispatcher();
        }
        $this->cartClass = $cartClass;
        $this->cartEvents = $cartEvents;
        $this->cartItemClass = $cartItemClass;
        $this->eventDispatcher = $eventDispatcher;
        $this->gateway = $gateway;
    }

    /**
     * @inheritdoc
     */
    public function addProductToOrder(OrderInterface $order, ProductInterface $product, array $options = null, $quantity = null)
    {
        $quantity = $quantity === null ? 1 : $quantity;

        return $this->doAddProductToOrder($order, $product, $options, $quantity);
    }

    /**
     * @inheritdoc
     */
    public function createCart($name = 'default')
    {
        $cart = new $this->cartClass();
        $cart->setName($name);
        $this->initOrder($cart);
        $this->gateway->persistOrder($cart);

        return $cart;
    }

    /**
     * @inheritdoc
     */
    public function createOrder($name = 'default')
    {
        $order = new $this->order();
        $order->setName($name);
        $this->initOrder($order);
        $this->gateway->persistOrder($order);

        return $order;
    }

    /**
     * @inheritdoc
     */
    public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        return $this->doFindBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * @inheritdoc
     */
    public function findOrdersBy(Query $query)
    {
        return $this->gateway->findOrders($query);
    }

    /**
     * @inheritdoc
     */
    public function findOrderById($id)
    {
        return $this->doFindOrderById($id);
    }

    /**
     * @inheritdoc
     */
    public function getEventDispatcher()
    {
        return $this->eventDispatcher;
    }

    /**
     * @inheritdoc
     */
    public function findProductInOrder(OrderInterface $cart, ProductInterface $product, array $options = null)
    {
        if ($items = $cart->getItems()) {
            foreach ($cart->getItems() as $item) {
                if ($item->getProduct() == $product) {
                    if ($this->doOptionsMatch($item->getOptions(), $options)) {
                        return $item;
                    }
                };
            }
        }

        return null;
    }

    /**
     * @inheritdoc
     */
    public function setOrderPricingSet(OrderInterface $cart, PricingSetInterface $pricingSet)
    {
        $rp = new \ReflectionProperty($cart, 'pricingSet');
        $rp->setAccessible(true);
        $rp->setValue($cart, $pricingSet);
        $rp->setAccessible(false);
    }

    /**
     * @inheritdoc
     */
    public function removeProductFromOrder(OrderInterface $cart, ProductInterface $product, array $options = null, $flush = true)
    {
        if (!$options) {
            $options = array();
        }
        $this->doRemoveItemFromOrder($cart, $product, $options);
    }

    /**
     * @inheritdoc
     */
    public function setOrderItemState(ItemInterface $cartItem, $state)
    {
        $rp = new \ReflectionProperty($cartItem, 'state');
        $rp->setAccessible(true);
        $rp->setValue($cartItem, $state);
        $rp->setAccessible(false);

        $cartEvents = $this->cartEvents;
        $this->eventDispatcher->dispatch($cartEvents::UPDATE_ITEM_STATE, $this->eventDispatcher->createEvent($cartItem));
    }

    /**
     * @inheritdoc
     */
    public function setOrderState(OrderInterface $cart, $state)
    {
        $rp = new \ReflectionProperty($cart, 'state');
        $rp->setAccessible(true);
        $rp->setValue($cart, $state);
        $rp->setAccessible(false);

        $cartEvents = $this->cartEvents;
        $this->eventDispatcher->dispatch($cartEvents::UPDATE_CART_STATE, $this->eventDispatcher->createEvent($cart));
    }

    public function setItemQuantity(ItemInterface $item, $quantity)
    {
        // todo: trigger events

        $rm = new \ReflectionMethod($item, 'setQuantity');
        $rm->setAccessible(true);
        $rm->invokeArgs($item, array($quantity));
        $rm->setAccessible(false);

        $cartEvents = $this->cartEvents;
        $this->eventDispatcher->dispatch($cartEvents::UPDATE_ITEM, $this->eventDispatcher->createEvent($item));
    }

    /**
     * @inheritdoc
     */
    public function setProductQuantity(OrderInterface $cart, ProductInterface $product, array $options, $quantity)
    {
        $item = $this->findProductInOrder($cart, $product, $options);
        $this->setItemQuantity($item, $quantity);
    }

    /**
     * @inheritdoc
     */
    public function updateOrder(OrderInterface $cart, $andPersist = true)
    {
        $cartEvents = $this->cartEvents;
        $this->eventDispatcher->dispatch($cartEvents::UPDATE_CART, $this->eventDispatcher->createEvent($cart));
        $this->doUpdateOrder($cart, $andPersist);
    }

    protected function createItem(ProductInterface $product, array $options = null)
    {
        $item = new $this->cartItemClass();

        $rm = new \ReflectionMethod($item, 'setProduct');
        $rm->setAccessible(true);
        $rm->invokeArgs($item, array($product));
        $rm->setAccessible(false);

        if ($options) {
            $rm = new \ReflectionMethod($item, 'setOptions');
            $rm->setAccessible(true);
            $rm->invokeArgs($item, array($options));
            $rm->setAccessible(false);
        }
        $this->initOrderItem($item);

        return $item;
    }

    /**
     * @inheritdoc
     */
    protected function doFindBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        $qb = new QueryBuilder();
        foreach($criteria as $field => $value) {
            $qb->field($field)->equals($value);
        }
        if ($orderBy) {
            $qb->orderBy($orderBy);
        }
        if ($limit) {
            $qb->limit($limit);
        }
        if ($offset) {
            $qb->offset($offset);
        }
        $query = $qb->getQuery();

        return $this->gateway->findOrders($query);
    }

    /**
     * @inheritdoc
     */
    protected function doFindOrderById($id)
    {
        $qb = new QueryBuilder();
        $qb->field('id')->equals($id);
        $query = $qb->getQuery();

        return $this->gateway->findOrders($query);
    }

    protected function doOptionsMatch($itemOptions, $targetOptions)
    {
        if (empty($targetOptions)) {
            if (empty($itemOptions)) {
                return true;
            } else {
                return false;
            }
        }
        if (empty($itemOptions)) {
            return false;
        }
        foreach ($targetOptions as $option) {
            if (!in_array($option, $itemOptions)) {
                return false;
            }
        }

        return true;
    }

    /**
     * @inheritdoc
     */
    protected function doUpdateOrder(OrderInterface $cart, $andPersist = true)
    {
        if ($andPersist) {
            $this->gateway->updateOrder($cart);
        }
    }

    protected function initOrder(OrderInterface $cart)
    {
        // Set default state (for now we set it to "open"), do this last since it will persist and flush the cart
        $cartClass = $this->cartClass;
        $this->setOrderState($cart, $cartClass::STATE_OPEN);

        //Delegate further initialization of the cart to those concerned
        $cartEvents = $this->cartEvents;
        $this->eventDispatcher->dispatch($cartEvents::INIT_CART, $this->eventDispatcher->createEvent($cart));
    }

    protected function doAddProductToOrder(OrderInterface $cart, ProductInterface $product, $options, $quantity)
    {
        if ($cartItem = $this->findProductInOrder($cart, $product, $options)) {
            $quantity = $cartItem->getQuantity() + $quantity;
            $this->setItemQuantity($cartItem, $quantity);

            return $cartItem;
        }

        $cartItem = $this->createItem($product, $options);
        $this->setItemQuantity($cartItem, $quantity);

        // add item to cart
        $rm = new \ReflectionMethod($cart, 'addItem');
        $rm->setAccessible(true);
        $rm->invokeArgs($cart, array($cartItem));
        $rm->setAccessible(false);

        $cartEvents = $this->cartEvents;
        $this->eventDispatcher->dispatch($cartEvents::INIT_ITEM, $this->eventDispatcher->createEvent($cartItem));

        return $cartItem;
    }

    protected function doRemoveItemFromOrder(OrderInterface $cart, ProductInterface $product, array $options)
    {
        if ($item = $this->findProductInOrder($cart, $product, $options)) {
            $rm = new \ReflectionMethod($cart, 'removeItem');
            $rm->setAccessible(true);
            $rm->invokeArgs($cart, array($item));
            $rm->setAccessible(false);
        }
    }

    protected function initOrderItem(ItemInterface $cartItem)
    {
        if ($product = $cartItem->getProduct()) {
            $cartItem->setName($product->getName());
        }
    }
}
