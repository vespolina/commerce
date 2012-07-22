<?php

use Vespolina\Cart\Event\CartEvents;
use Vespolina\Cart\Manager\CartManager;
use Vespolina\Cart\Pricing\DefaultCartPricingProvider;
use Vespolina\Entity\Order\Cart;
use Vespolina\Entity\Product;
use Vespolina\EventDispatcher\EventDispatcherInterface;
use Vespolina\EventDispatcher\EventInterface;

class CartManagerTest extends \PHPUnit_Framework_TestCase
{
    public function testConstruct()
    {
        $mgr = $this->createCartManager(null, null, null, null, null, null);
        $rp = new \ReflectionProperty($mgr, 'eventDispatcher');
        $rp->setAccessible(true);
        $dispatcher = $rp->getValue($mgr);
        $this->assertInstanceOf('Vespolina\EventDispatcher\NullDispatcher', $dispatcher, 'if a dispatcher is not passed set up the NullDispatcher');
    }

    public function testCreateCart()
    {
        $mgr = $this->createCartManager();
        $cart = $mgr->createCart('test');

        $this->assertInstanceOf('Vespolina\Entity\Order\Cart', $cart, 'it should be an instance of the cart class passed in the construct');
        $this->assertSame('test', $cart->getName(), 'the name of cart should have been set when it was created');
        $this->assertSame(Cart::STATE_OPEN, $cart->getState());
        $this->assertSame(CartEvents::INIT, $mgr->getEventDispatcher()->getLastEventName(), 'a CartEvents::INIT event should be triggered');

        $this->markTestIncomplete('the cart should be persisted through the gateway');
    }

    public function testFindProductInCart()
    {
        $mgr = $this->createCartManager();
        $cart = $mgr->createCart('test');

        $createItem = new \ReflectionMethod($mgr, 'createItem');
        $createItem->setAccessible(true);
        $addItem = new \ReflectionMethod($cart, 'addItem');
        $addItem->setAccessible(true);

        $product = new Product();
        $product->setName('test product');
        $testItem = $createItem->invokeArgs($mgr, array($product));
        $addItem->invokeArgs($cart, array($testItem));

        $item = $mgr->findProductInCart($cart, $product);
        $this->assertSame($product, $item->getProduct(), 'find the item that contains the product');

        $newProduct = new Product();
        $newProduct->setName('with options');
        $optionsBlue = array('color' => 'blue', 'size' => 'small');
        $blueItem = $createItem->invokeArgs($mgr, array($newProduct, $optionsBlue));
        $addItem->invokeArgs($cart, array($blueItem));

        $foundBlueItem = $mgr->findProductInCart($cart, $newProduct, $optionsBlue);
        $this->assertSame($newProduct, $foundBlueItem->getProduct(), 'find the item that contains the product with the options');
        $this->assertSame($optionsBlue, $foundBlueItem->getOptions(), 'find the item that contains the product with the options');

        $optionsRed = array('color' => 'red', 'size' => 'large');
        $redItem = $createItem->invokeArgs($mgr, array($newProduct, $optionsRed));
        $addItem->invokeArgs($cart, array($redItem));

        $foundRedItem = $mgr->findProductInCart($cart, $newProduct, array('size' => 'large', 'color' => 'red'));
        $this->assertNotSame($redItem, $blueItem);
        $this->assertSame($newProduct, $foundRedItem->getProduct(), 'find the item that contains the product with the options');
        $this->assertSame($optionsRed, $foundRedItem->getOptions(), 'find the item that contains the product with the options');

        $this->assertNull($mgr->findProductInCart($cart, $product, $optionsRed), "product and options don't match, nothing returned");
        $this->assertNull($mgr->findProductInCart($cart, $newProduct), 'this item has options, so nothing returned');
        $this->assertNull($mgr->findProductInCart($cart, $newProduct, array('color' => 'yellow')), 'no yellow options set');
    }

    public function testAddProductToCart()
    {
        $mgr = $this->createCartManager();
        $cart = $mgr->createCart('test');

        $product = new Product();
        $product->setName('test product');

        $mgr->addProductToCart($cart, $product);

        $items = $cart->getItems();
        $this->assertSame(1, count($items));
        $item = $items[0];
        $this->assertSame($product, $item->getProduct());
        $this->assertSame(1, $item->getQuantity());
        $this->assertSame('test product', $item->getName());

        $existingItem = $mgr->addProductToCart($cart, $product);
        $this->assertSame($existingItem, $item);
        $items = $cart->getItems();
        $this->assertSame(1, count($items));
        $this->assertSame(2, $existingItem->getQuantity());

        $mgr->addProductToCart($cart, $product, array(), 2);

        $this->assertSame(4, $existingItem->getQuantity(), 'passing the quantity should add to the existing quantity');

        $optionSet1 = array('color' => 'blue', 'size' => 'small');
        $optionSet2 = array('color' => 'red', 'size' => 'small');

        $option1Item = $mgr->addProductToCart($cart, $product, $optionSet1);
        $this->assertNotSame($option1Item, $existingItem, 'different options for the same product should be different items');
        $items = $cart->getItems();
        $this->assertSame(2, count($items));

        $option2Item = $mgr->addProductToCart($cart, $product, $optionSet2, 3);
        $this->assertNotSame($option1Item, $option2Item, 'different options for the same product should be different items');
        $items = $cart->getItems();
        $this->assertSame(3, count($items));
        $this->assertSame(3, $option2Item->getQuantity());
    }

    public function testRemoveProductFromCart()
    {
        $mgr = $this->createCartManager();
        $cart = $mgr->createCart();

        $product = new Product();
        $options = array('size' => 'large');

        $item = $mgr->addProductToCart($cart, $product);
        $this->assertCount(1, $cart->getItems(), 'verify item is in cart');
        $mgr->removeProductFromCart($cart, $product, $options);
        $this->assertContains($item, $cart->getItems(), "the items should still be in the cart since the item didn't have options");
        $mgr->removeProductFromCart($cart, $product);
        $this->assertEmpty($cart->getItems(), 'the cart should be empty again');

        $mgr->addProductToCart($cart, $product, $options);
        $mgr->removeProductFromCart($cart, $product, $options);
        $this->assertEmpty($cart->getItems(), 'removing product with options');

        $item = $mgr->addProductToCart($cart, $product, $options);
        $mgr->removeProductFromCart($cart, $product);
        $this->assertContains($item, $cart->getItems(), 'the items should still be in the cart since options were not passed');
        $mgr->removeProductFromCart($cart, $product, array('size' => 'small'));
        $this->assertContains($item, $cart->getItems(), 'the items should still be in the cart since the wrong options were passed');

    }

    public function testSetCartItemState()
    {
        $mgr = $this->createCartManager();
        $cart = $mgr->createCart();
        $product = new Product();

        $item = $mgr->addProductToCart($cart, $product);

        $this->assertNotSame('test', $item->getState(), "make sure the state isn't set to test");
        $mgr->setCartItemState($item, 'test');
        $this->assertSame('test', $item->getState(), "the state should now be set to test");
    }

    public function testSetCartState()
    {
        $this->markTestIncomplete('write the damn test');

    }

    public function testSetProductQuantity()
    {
        $this->markTestIncomplete('write the damn test');

    }

    public function testUpdateCart()
    {
        $this->markTestIncomplete('write the damn test');

    }

    protected function createCartManager($pricingProvider = null, $cartClass = null, $cartItemClass = null, $cartEvents = null, $eventClass = null, $dispatcherClass = 'TestDispatcher')
    {
        if (!$pricingProvider) {
            $pricingProvider = new DefaultCartPricingProvider();
        }
        if (!$cartClass) {
            $cartClass = 'Vespolina\Entity\Order\Cart';
        }
        if (!$cartItemClass) {
            $cartItemClass = 'Vespolina\Entity\Order\Item';
        }
        if (!$cartEvents) {
            $cartEvents = 'Vespolina\Cart\Event\CartEvents';
        }
        if (!$eventClass) {
            $eventClass = 'Vespolina\EventDispatcher\Event';
        }
        if ($dispatcherClass) {
            $eventDispatcher = new $dispatcherClass();
        } else {
            $eventDispatcher = null;
        }

        return new CartManager($pricingProvider, $cartClass, $cartItemClass, $cartEvents, $eventClass, $eventDispatcher);
    }
}

class TestDispatcher implements EventDispatcherInterface
{
    protected $lastEvent;
    protected $lastEventName;

    public function dispatch($eventName, EventInterface $event = null)
    {
        $this->lastEvent = $event;
        $this->lastEventName = $eventName;
    }

    public function getLastEvent()
    {
        return $this->lastEvent;
    }

    public function getLastEventName()
    {
        return $this->lastEventName;
    }
}
