<?php
/**
 * (c) 2011-2012 Vespolina Project http://www.vespolina-project.org
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Vespolina\CartBundle\Model;

use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\HttpKernel\Bundle\Bundle;

use Vespolina\CartBundle\CartEvents;
use Vespolina\CartBundle\Event\CartEvent;
use Vespolina\CartBundle\Event\CartPricingEvent;
use Vespolina\Entity\ProductInterface;
use Vespolina\Entity\OrderInterface;
use Vespolina\CartBundle\Model\CartItemInterface;
use Vespolina\CartBundle\Model\CartManagerInterface;
use Vespolina\CartBundle\Pricing\CartPricingProviderInterface;

/**
 * @author Daniel Kucharski <daniel@xerias.be>
 * @author Richard Shank <develop@zestic.com>
 */
abstract class CartManager implements CartManagerInterface
{
    protected $cartClass;
    protected $cartItemClass;
    protected $dispatcher;
    protected $pricingProvider;
    protected $recurringInterface;

    // todo: $recurringInterface should be handled in a handler
    function __construct(CartPricingProviderInterface $pricingProvider, $cartClass, $cartItemClass, $recurringInterface = 'Vespolina\ProductSubscriptionBundle\Model\RecurringInterface')
    {
        $this->cartClass = $cartClass;
        $this->cartItemClass = $cartItemClass;
        $this->pricingProvider = $pricingProvider;
        $this->recurringInterface = $recurringInterface;
    }

    /**
     * @inheritdoc
     */
    public function addItemToCart(CartInterface $cart, ProductInterface $product)
    {
        $item = $this->doAddItemToCart($cart, $product);

        return $item;
    }

    /**
     * @inheritdoc
     */
    public function createCart($cartType = 'default')
    {
        $cart = new $this->cartClass($cartType);
        $this->initCart($cart);

        return $cart;
    }

    /**
     * @inheritdoc
     */
    public function createItem(ProductInterface $product = null)
    {
        $cartItem = new $this->cartItemClass($product);
        $this->initCartItem($cartItem);

        return $cartItem;
    }

    /**
     * @inheritdoc
     */
    public function determinePrices(CartInterface $cart, $determineItemPrices = true)
    {
        $pricingProvider = $this->getPricingProvider();
        $pricingContext = $pricingProvider->createPricingContext();

        //Init the pricing context container and have it filled if required through the event dispatcher
        if (null != $this->dispatcher) {
            $this->dispatcher->dispatch(CartEvents::CART_INIT_PRICING_CONTEXT,  new CartPricingEvent($cart, $pricingContext));
        }

        $pricingProvider->determineCartPrices($cart, $pricingContext, $determineItemPrices);
    }

    /**
     * @inheritdoc
     */
    public function finishCart(CartInterface $cart)
    {
        if (null != $this->dispatcher) {
            $this->dispatcher->dispatch(CartEvents::CART_FINISHED,  new CartEvent($cart));
        }
    }

    /**
     * @inheritdoc
     */
    public function getPricingProvider()
    {
        return $this->pricingProvider;
    }

    /**
     * @inheritdoc
     */
    public function initCart(CartInterface $cart)
    {
        // Create the pricing set to hold cart level pricing data
        $this->setCartPricingSet($cart, $this->pricingProvider->createPricingSet());

        // Set default state (for now we set it to "open"), do this last since it will persist and flush the cart
        $this->setCartState($cart, Cart::STATE_OPEN);

        //Delegate further initialization of the cart to those concerned
        if (null != $this->dispatcher) {
            $this->dispatcher->dispatch(CartEvents::CART_INIT,  new CartEvent($cart));
        }
    }

    public function initCartItem(CartItemInterface $cartItem)
    {
        // todo: this should be moved into a handler
        //Default cart item description to the product name
        if ($product = $cartItem->getProduct()) {
            $cartItem->setName($product->getName());
            $cartItem->setDescription($cartItem->getName());
            $rpPricingSet = new \ReflectionProperty($cartItem, 'pricingSet');
            $rpPricingSet->setAccessible(true);
            $rpPricingSet->setValue($cartItem, $this->getPricingProvider()->createPricingSet());
            $rpPricingSet->setAccessible(false);
            // todo: especially this damn thing, and get the Interface out of the __construct
            if ($product instanceof $this->recurringInterface) {
                $rp = new \ReflectionProperty($cartItem, 'isRecurring');
                $rp->setAccessible(true);
                $rp->setValue($cartItem, true);
                $rp->setAccessible(false);
            }
        }
    }

    public function setCartPricingSet(CartInterface $cart, $pricingSet)
    {
        $rp = new \ReflectionProperty($cart, 'pricingSet');
        $rp->setAccessible(true);
        $rp->setValue($cart, $pricingSet);
        $rp->setAccessible(false);
    }

    public function setEventDispatcher($dispatcher) {

        $this->dispatcher = $dispatcher;
    }

    public function setCartItemState(CartItemInterface $cartItem, $state)
    {
        $rp = new \ReflectionProperty($cartItem, 'state');
        $rp->setAccessible(true);
        $rp->setValue($cartItem, $state);
        $rp->setAccessible(false);
    }

    public function setCartState(CartInterface $cart, $state)
    {
        $rp = new \ReflectionProperty($cart, 'state');
        $rp->setAccessible(true);
        $rp->setValue($cart, $state);
        $rp->setAccessible(false);
    }

    public function findItemInCart(CartInterface $cart, ProductInterface $product)
    {
        foreach ($cart->getItems() as $item) {
            if ($item->getProduct() == $product) {
                return $item;
            };
        }

        return null;
    }

    public function removeItemFromCart(CartInterface $cart, ProductInterface $product, $flush = true)
    {
        $this->doRemoveItemFromCart($cart, $product);
    }

    public function setItemQuantity(CartItemInterface $cartItem, $quantity)
    {
        // add item to cart
        $rm = new \ReflectionMethod($cartItem, 'setQuantity');
        $rm->setAccessible(true);
        $rm->invokeArgs($cartItem, array($quantity));
        $rm->setAccessible(false);
    }

    protected function doAddItemToCart(CartInterface $cart, ProductInterface $product)
    {
        if ($cartItem = $this->findItemInCart($cart, $product)) {
            $quantity = $cartItem->getQuantity() + 1;
            $this->setItemQuantity($cartItem, $quantity);

            return $cartItem;
        }

        $item = $this->createItem($product);

        // add item to cart
        $rm = new \ReflectionMethod($cart, 'addItem');
        $rm->setAccessible(true);
        $rm->invokeArgs($cart, array($item));
        $rm->setAccessible(false);

        return $item;
    }

    protected function doRemoveItemFromCart(CartInterface $cart, ProductInterface $product)
    {
        $item = $this->findItemInCart($cart, $product);

        // add item to cart
        $rm = new \ReflectionMethod($cart, 'removeItem');
        $rm->setAccessible(true);
        $rm->invokeArgs($cart, array($item));
        $rm->setAccessible(false);
    }
}
