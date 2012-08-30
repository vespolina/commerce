<?php
/**
 * (c) 2011-2012 Vespolina Project http://www.vespolina-project.org
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Vespolina\Cart\Manager;

use Gateway\Query;
use Gateway\QueryBuilder;
use Vespolina\Entity\Order\CartEvents;
use Vespolina\Cart\Manager\CartManagerInterface;
use Vespolina\Cart\Gateway\CartGatewayInterface;
use Vespolina\Entity\Pricing\PricingSetInterface;
use Vespolina\Entity\Order\Cart;
use Vespolina\Entity\Order\CartInterface;
use Vespolina\Entity\Order\ItemInterface;
use Vespolina\Entity\Order\OrderInterface;
use Vespolina\Entity\ProductInterface;
use Vespolina\EventDispatcher\EventDispatcherInterface;
use Vespolina\EventDispatcher\NullDispatcher;

/**
 * @author Daniel Kucharski <daniel@xerias.be>
 * @author Richard Shank <develop@zestic.com>
 */
class CartManager implements CartManagerInterface
{
    protected $cartClass;
    protected $cartItemClass;
    protected $eventDispatcher;
    protected $gateway;

    function __construct(CartGatewayInterface $gateway, $cartClass, $cartItemClass, $cartEvents, EventDispatcherInterface $eventDispatcher = null)
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
    public function addProductToCart(CartInterface $cart, ProductInterface $product, array $options = null, $quantity = null)
    {
        $quantity = $quantity === null ? 1 : $quantity;

        return $this->doAddProductToCart($cart, $product, $options, $quantity);
    }

    /**
     * @inheritdoc
     */
    public function createCart($name = 'default')
    {
        $cart = new $this->cartClass();
        $cart->setName($name);
        $this->initCart($cart);
        $this->gateway->persistCart($cart);

        return $cart;
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
    public function findCartsBy(Query $query)
    {
        return $this->gateway->findCarts($query);
    }

    /**
     * @inheritdoc
     */
    public function findCartById($id)
    {
        return $this->doFindCartById($id);
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
    public function findProductInCart(CartInterface $cart, ProductInterface $product, array $options = null)
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
    public function setCartPricingSet(CartInterface $cart, PricingSetInterface $pricingSet)
    {
        $rp = new \ReflectionProperty($cart, 'pricingSet');
        $rp->setAccessible(true);
        $rp->setValue($cart, $pricingSet);
        $rp->setAccessible(false);
    }

    /**
     * @inheritdoc
     */
    public function removeProductFromCart(CartInterface $cart, ProductInterface $product, array $options = null, $flush = true)
    {
        if (!$options) {
            $options = array();
        }
        $this->doRemoveItemFromCart($cart, $product, $options);
    }

    /**
     * @inheritdoc
     */
    public function setCartItemState(ItemInterface $cartItem, $state)
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
    public function setCartState(CartInterface $cart, $state)
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
    public function setProductQuantity(CartInterface $cart, ProductInterface $product, array $options, $quantity)
    {
        $item = $this->findProductInCart($cart, $product, $options);
        $this->setItemQuantity($item, $quantity);
    }

    /**
     * @inheritdoc
     */
    public function updateCart(CartInterface $cart, $andPersist = true)
    {
        $cartEvents = $this->cartEvents;
        $this->eventDispatcher->dispatch($cartEvents::UPDATE_CART, $this->eventDispatcher->createEvent($cart));
        $this->doUpdateCart($cart, $andPersist);
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
        $this->initCartItem($item);

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

        return $this->gateway->findCarts($query);
    }

    /**
     * @inheritdoc
     */
    protected function doFindCartById($id)
    {
        $qb = new QueryBuilder();
        $qb->field('id')->equals($id);
        $query = $qb->getQuery();

        return $this->gateway->findCarts($query);
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
    protected function doUpdateCart(CartInterface $cart, $andPersist = true)
    {
        if ($andPersist) {
            $this->gateway->updateCart($cart);
        }
    }

    protected function initCart(CartInterface $cart)
    {
        // Set default state (for now we set it to "open"), do this last since it will persist and flush the cart
        $cartClass = $this->cartClass;
        $this->setCartState($cart, $cartClass::STATE_OPEN);

        //Delegate further initialization of the cart to those concerned
        $cartEvents = $this->cartEvents;
        $this->eventDispatcher->dispatch($cartEvents::INIT_CART, $this->eventDispatcher->createEvent($cart));
    }

    protected function doAddProductToCart(CartInterface $cart, ProductInterface $product, $options, $quantity)
    {
        if ($cartItem = $this->findProductInCart($cart, $product, $options)) {
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

    protected function doRemoveItemFromCart(CartInterface $cart, ProductInterface $product, array $options)
    {
        if ($item = $this->findProductInCart($cart, $product, $options)) {
            $rm = new \ReflectionMethod($cart, 'removeItem');
            $rm->setAccessible(true);
            $rm->invokeArgs($cart, array($item));
            $rm->setAccessible(false);
        }
    }

    protected function initCartItem(ItemInterface $cartItem)
    {
        if ($product = $cartItem->getProduct()) {
            $cartItem->setName($product->getName());
        }
    }
}
