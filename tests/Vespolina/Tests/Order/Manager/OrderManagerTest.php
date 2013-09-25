<?php

use Vespolina\Entity\Order\CartEvents;
use Vespolina\Order\Gateway\OrderMemoryGateway;
use Vespolina\Order\Manager\OrderManager;
use Vespolina\Order\Pricing\DefaultCartPricingProvider;
use Vespolina\Entity\Order\Cart;
use Vespolina\Entity\Product\Product;
use Vespolina\EventDispatcher\EventDispatcherInterface;
use Vespolina\EventDispatcher\EventInterface;
use Vespolina\Entity\Partner\Partner;

use Vespolina\Tests\Order\OrderTestsCommon;

class OrderManagerTest extends \PHPUnit_Framework_TestCase
{
    static $gateway;

    public function setUp()
    {
        self::$gateway = $this->getMockBuilder('Vespolina\Order\Gateway\OrderGatewayInterface')
            ->disableOriginalConstructor()
            ->getMock()
        ;
    }

    public function testConstructDispatcher()
    {
        $mgr = OrderTestsCommon::getOrderManager();
        $rp = new \ReflectionProperty($mgr, 'eventDispatcher');
        $rp->setAccessible(true);
        $dispatcher = $rp->getValue($mgr);
        $this->assertInstanceOf('Vespolina\EventDispatcher\NullDispatcher', $dispatcher, 'if a dispatcher is not passed set up the NullDispatcher');
    }

    public function testCreateCart()
    {
        $mgr = $this->createOrderManager();
        $cart = $mgr->createCart('test');

        $this->assertInstanceOf('Vespolina\Entity\Order\Cart', $cart, 'it should be an instance of the cart class passed in the construct');
        $this->assertSame('test', $cart->getName(), 'the name of cart should have been set when it was created');
        $this->assertSame(Cart::STATE_OPEN, $cart->getState());

        //$this->assertSame(CartEvents::INIT_ORDER, $mgr->getEventDispatcher()->getLastEventName(), 'a CartEvents::INIT_CART event should be triggered');
        //$event = $mgr->getEventDispatcher()->getLastEvent();
        //$this->assertInstanceOf('Vespolina\Entity\Order\CartInterface', $event->getSubject());

        $this->verifyPersistence($cart);
    }

    public function testFindCartById()
    {
        $mgr = $this->createOrderManager();

        $cart1 = $mgr->createCart('findCart1');
        $cart2 = $mgr->createCart('findCart2');

        $cartId = $cart1->getId();

        $loadedCart = $mgr->findOrderById($cartId);

        $this->assertInstanceOf('Vespolina\Entity\Order\Cart', $loadedCart);
        $this->assertSame($cart1, $loadedCart);
    }

    public function testfindProductInOrder()
    {
        $mgr = $this->createOrderManager();
        $cart = $mgr->createCart('test');

        $createItem = new \ReflectionMethod($mgr, 'createItem');
        $createItem->setAccessible(true);
        $addItem = new \ReflectionMethod($cart, 'addItem');
        $addItem->setAccessible(true);

        $product = new Product();
        $product->setName('test product');
        $testItem = $createItem->invokeArgs($mgr, array($product));
        $addItem->invokeArgs($cart, array($testItem));

        $item = $mgr->findProductInOrder($cart, $product);
        $this->assertSame($product, $item->getProduct(), 'find the item that contains the product');

        $newProduct = new Product();
        $newProduct->setName('with options');
        $optionsBlue = array('color' => 'blue', 'size' => 'small');
        $blueItem = $createItem->invokeArgs($mgr, array($newProduct, $optionsBlue));
        $addItem->invokeArgs($cart, array($blueItem));

        $foundBlueItem = $mgr->findProductInOrder($cart, $newProduct, $optionsBlue);
        $this->assertSame($newProduct, $foundBlueItem->getProduct(), 'find the item that contains the product with the options');
        $this->assertSame($optionsBlue, $foundBlueItem->getOptions(), 'find the item that contains the product with the options');

        $optionsRed = array('color' => 'red', 'size' => 'large');
        $redItem = $createItem->invokeArgs($mgr, array($newProduct, $optionsRed));
        $addItem->invokeArgs($cart, array($redItem));

        $foundRedItem = $mgr->findProductInOrder($cart, $newProduct, array('size' => 'large', 'color' => 'red'));
        $this->assertNotSame($redItem, $blueItem);
        $this->assertSame($newProduct, $foundRedItem->getProduct(), 'find the item that contains the product with the options');
        $this->assertSame($optionsRed, $foundRedItem->getOptions(), 'find the item that contains the product with the options');

        $this->assertNull($mgr->findProductInOrder($cart, $product, $optionsRed), "product and options don't match, nothing returned");
        $this->assertNull($mgr->findProductInOrder($cart, $newProduct), 'this item has options, so nothing returned');
        $this->assertNull($mgr->findProductInOrder($cart, $newProduct, array('color' => 'yellow')), 'no yellow options set');
    }


    public function testFindOpenCartByOwner()
    {
        $owner = new Partner('person');

        $this->dm->persist($owner);
        $this->dm->flush();

        $cart = $this->cartMgr->createCart();
        $cart->setOwner($owner);
        $this->cartMgr->updateCart($cart);

        $ownersCart = $this->cartMgr->findOpenCartByOwner($owner);
        $this->assertSame($cart->getId(), $ownersCart->getId());

        $this->cartMgr->setCartState($cart, Cart::STATE_CLOSED);
        $this->assertNull($this->cartMgr->findOpenCartByOwner($owner));

        return $cart;
    }

    public function testGetActiveCartForOwner()
    {
        $owner = new Partner('person');

        $this->dm->persist($owner);
        $this->dm->flush();

        $session = $this->container->get('session');
        // not really a test, but it does make sure we start empty
        $this->assertNull($session->get('vespolina_cart'));

        $firstPassCart = $this->cartMgr->getActiveCart($owner);
        $persistedCarts = $this->cartMgr->findBy(array());
        $this->assertSame(1, $persistedCarts->count(), 'there should only be one cart in the db');
        $this->assertSame($firstPassCart, $session->get('vespolina_cart'), 'the new cart should have been set for the session');

        $secondPassCart = $this->cartMgr->getActiveCart($owner);
        $this->assertSame($firstPassCart->getId(), $secondPassCart->getId());
        $this->assertSame(1, $persistedCarts->count(), 'there should only be one cart in the db');

        $session->clear('vespolina_cart');
        $thirdPassCart = $this->cartMgr->getActiveCart($owner);
        $this->assertSame($firstPassCart->getId(), $thirdPassCart->getId());
        $this->assertSame(1, $persistedCarts->count(), 'there should only be one cart in the db');
        $this->assertSame($thirdPassCart, $session->get('vespolina_cart'), 'the new cart should have been set for the session');
    }


    public function testGetActiveCartWithoutOwner()
    {
        $session = $this->container->get('session');
        // not really a test, but it does make sure we start empty
        $this->assertNull($session->get('vespolina_cart'));

        $firstPassCart = $this->cartMgr->getActiveCart();
        $persistedCarts = $this->cartMgr->findBy(array());
        $this->assertSame(1, $persistedCarts->count(), 'there should only be one cart in the db');
        $this->assertSame($firstPassCart, $session->get('vespolina_cart'), 'the new cart should have been set for the session');

        $secondPassCart = $this->cartMgr->getActiveCart();
        $this->assertSame($firstPassCart->getId(), $secondPassCart->getId());
        $this->assertSame(1, $persistedCarts->count(), 'there should only be one cart in the db');

        $session->clear('vespolina_cart');
        $thirdPassCart = $this->cartMgr->getActiveCart();
        $this->assertNotSame($firstPassCart->getId(), $thirdPassCart->getId());
        $this->assertSame(2, $persistedCarts->count(), 'there is a left over cart, this should probably be handled');
        $this->assertSame($thirdPassCart, $session->get('vespolina_cart'), 'the new cart should have been set for the session');
    }

    public function testaddProductToOrder()
    {
        $mgr = $this->createOrderManager();
        $cart = $mgr->createCart('test');

        $product = new Product();
        $product->setName('test product');

        $mgr->addProductToOrder($cart, $product);

        $items = $cart->getItems();
        $this->assertSame(1, count($items));
        $item = $items[0];
        $this->assertSame($product, $item->getProduct());
        $this->assertSame(1, $item->getQuantity());
        $this->assertSame('test product', $item->getName());

        // add the same product again to increase the quantity
        $existingItem = $mgr->addProductToOrder($cart, $product);
        $this->assertSame($existingItem, $item);
        $items = $cart->getItems();
        $this->assertSame(1, count($items));
        $this->assertSame(2, $existingItem->getQuantity());

        //$this->assertSame(CartEvents::UPDATE_ITEM, $mgr->getEventDispatcher()->getLastEventName(), 'a CartEvents::UPDATE_ITEM event should be triggered');
        //$event = $mgr->getEventDispatcher()->getLastEvent();
        //$this->assertInstanceOf('Vespolina\Entity\Order\ItemInterface', $event->getSubject());

        // specifiy the quantity when adding a product to the cart
        $mgr->addProductToOrder($cart, $product, array(), 2);
        $this->assertSame(4, $existingItem->getQuantity(), 'passing the quantity should add to the existing quantity');

        //$this->assertSame(CartEvents::UPDATE_ITEM, $mgr->getEventDispatcher()->getLastEventName(), 'a CartEvents::UPDATE_ITEM event should be triggered');
        //$event = $mgr->getEventDispatcher()->getLastEvent();
        //$this->assertInstanceOf('Vespolina\Entity\Order\ItemInterface', $event->getSubject());

        $optionSet1 = array('color' => 'blue', 'size' => 'small');
        $optionSet2 = array('color' => 'red', 'size' => 'small');

        $option1Item = $mgr->addProductToOrder($cart, $product, $optionSet1);
        $this->assertNotSame($option1Item, $existingItem, 'different options for the same product should be different items');
        $items = $cart->getItems();
        $this->assertSame(2, count($items));

        $option2Item = $mgr->addProductToOrder($cart, $product, $optionSet2, 3);
        $this->assertNotSame($option1Item, $option2Item, 'different options for the same product should be different items');
        $items = $cart->getItems();
        $this->assertSame(3, count($items));
        $this->assertSame(3, $option2Item->getQuantity());

        //$this->assertSame(CartEvents::INIT_ITEM, $mgr->getEventDispatcher()->getLastEventName(), 'a CartEvents::INIT_ITEM event should be triggered');
        //$event = $mgr->getEventDispatcher()->getLastEvent();
        //$this->assertInstanceOf('Vespolina\Entity\Order\ItemInterface', $event->getSubject());
    }

    public function testFindItemInCart()
    {
        $cart = $this->persistNewCart();
        $cartable = $this->persistNewCartable('product');
        $addedItem = $this->cartMgr->addItemToCart($cart, $cartable);

        $item = $this->cartMgr->findItemInCart($cart, $cartable);

        $this->assertSame($item, $addedItem);
    }


    public function testRemoveItemFromCart()
    {
        $cart = $this->persistNewCart();
        $cartable = $this->persistNewCartable('product');
        $this->cartMgr->addItemToCart($cart, $cartable);
        $this->cartMgr->removeItemFromCart($cart, $cartable);

        $items = $cart->getItems();
        $this->assertSame(0, $items->count());
    }

    public function testremoveProductFromOrder()
    {
        $mgr = $this->createOrderManager();
        $cart = $mgr->createCart();

        $product = new Product();
        $options = array('size' => 'large');

        $item = $mgr->addProductToOrder($cart, $product);
        $this->assertCount(1, $cart->getItems(), 'verify item is in cart');
        $mgr->removeProductFromOrder($cart, $product, $options);
        $this->assertContains($item, $cart->getItems(), "the items should still be in the cart since the item didn't have options");
        $mgr->removeProductFromOrder($cart, $product);
        $this->assertEmpty($cart->getItems(), 'the cart should be empty again');

        $mgr->addProductToOrder($cart, $product, $options);
        $mgr->removeProductFromOrder($cart, $product, $options);
        $this->assertEmpty($cart->getItems(), 'removing product with options');

        $item = $mgr->addProductToOrder($cart, $product, $options);
        $mgr->removeProductFromOrder($cart, $product);
        $this->assertContains($item, $cart->getItems(), 'the items should still be in the cart since options were not passed');
        $mgr->removeProductFromOrder($cart, $product, array('size' => 'small'));
        $this->assertContains($item, $cart->getItems(), 'the items should still be in the cart since the wrong options were passed');

    }
    public function testSetCartState()
    {
        $cart = $this->persistNewCart();

        $persistedCart = $this->cartMgr->findCartById($cart->getId());
        $this->assertSame(Cart::STATE_OPEN, $persistedCart->getState(), 'the cart should start in an open state');

        $this->cartMgr->setCartState($cart, 'close');
        $persistedCart = $this->cartMgr->findCartById($cart->getId());
        $this->assertSame('close', $persistedCart->getState());
    }

    public function testsetOrderItemState()
    {
        $mgr = $this->createOrderManager();
        $cart = $mgr->createCart();
        $product = new Product();

        $item = $mgr->addProductToOrder($cart, $product);

        $this->assertNotSame('test', $item->getState(), "make sure the state isn't set to test");
        $mgr->setOrderItemState($item, 'test');
        $this->assertSame('test', $item->getState(), "the state should now be set to test");

        //$this->assertSame(CartEvents::UPDATE_ITEM_STATE, $mgr->getEventDispatcher()->getLastEventName(), 'a CartEvents::UPDATE_ITEM_STATE event should be triggered');
        //$event = $mgr->getEventDispatcher()->getLastEvent();
        //$this->assertInstanceOf('Vespolina\Entity\Order\ItemInterface', $event->getSubject());
    }

    public function testsetOrderState()
    {
        $mgr = $this->createOrderManager();
        $cart = $mgr->createCart();

        $this->assertNotSame('test', $cart->getState(), "make sure the state isn't set to test");
        $mgr->setOrderState($cart, 'test');
        $this->assertSame('test', $cart->getState(), "the state should now be set to test");

        //$this->assertSame(CartEvents::UPDATE_CART_STATE, $mgr->getEventDispatcher()->getLastEventName(), 'a CartEvents::UPDATE_CART_STATE event should be triggered');
        //$event = $mgr->getEventDispatcher()->getLastEvent();
        //$this->assertInstanceOf('Vespolina\Entity\Order\CartInterface', $event->getSubject());
    }

    public function testSetItemQuantity()
    {
        $mgr = $this->createOrderManager();
        $cart = $mgr->createCart();

        $product = new Product();
        $item = $mgr->addProductToOrder($cart, $product);

        $mgr->setItemQuantity($item, 5);
        $this->assertSame(5, $item->getQuantity(), 'the quantity should be updated');

        //$this->assertSame(CartEvents::UPDATE_ITEM, $mgr->getEventDispatcher()->getLastEventName(), 'a CartEvents::UPDATE_ITEM event should be triggered');
        //$event = $mgr->getEventDispatcher()->getLastEvent();
        //$this->assertInstanceOf('Vespolina\Entity\Order\ItemInterface', $event->getSubject());
    }

    public function testSetProductQuantity()
    {
        $mgr = $this->createOrderManager();
        $cart = $mgr->createCart();

        $product = new Product();
        $item = $mgr->addProductToOrder($cart, $product);
        $mgr->setProductQuantity($cart, $product, array(), 5);
        $this->assertSame(5, $item->getQuantity(), 'the quantity should be updated');

        $options = array('size' => 'large');
        $optionItem = $mgr->addProductToOrder($cart, $product, $options);
        $mgr->setProductQuantity($cart, $product, $options, 5);
        $this->assertSame(5, $optionItem->getQuantity(), 'the quantity should be updated');

        //$this->assertSame(CartEvents::UPDATE_ITEM, $mgr->getEventDispatcher()->getLastEventName(), 'a CartEvents::UPDATE_ITEM event should be triggered');
        //$event = $mgr->getEventDispatcher()->getLastEvent();
        //$this->assertInstanceOf('Vespolina\Entity\Order\ItemInterface', $event->getSubject());
    }

    public function testUpdateOrder()
    {
        $mgr = $this->createOrderManager();
        $cart = $mgr->createCart('testupdateOrder');
        $dummyCart = $mgr->createCart('toMakeSureTestupdateOrderIsNotLastCart');

        $mgr->updateOrder($cart, false);

        //$this->assertSame(CartEvents::UPDATE_CART, $mgr->getEventDispatcher()->getLastEventName(), 'a CartEvents::UPDATE_CART event should be triggered');
        //$event = $mgr->getEventDispatcher()->getLastEvent();
        //$this->assertInstanceOf('Vespolina\Entity\Order\CartInterface', $event->getSubject());

        $this->verifyPersistence($dummyCart); // should still be dummy cart since persist parameter was false

       $mgr->createCart('toMakeSureLastEventIsCreate');

        $mgr->updateOrder($cart);
        //$this->assertSame(CartEvents::UPDATE_CART, $mgr->getEventDispatcher()->getLastEventName(), 'a CartEvents::UPDATE_CART event should be triggered');
        //$event = $mgr->getEventDispatcher()->getLastEvent();
        //$this->assertInstanceOf('Vespolina\Entity\Order\CartInterface', $event->getSubject());

        $this->verifyPersistence($cart);
    }

    protected function createOrderManager($gateway = null, $cartClass = null, $cartItemClass = null, $cartEvents = null, $dispatcherClass = 'TestDispatcher')
    {
        if (!$gateway) {
            $gateway = self::$gateway;
        }

        if (!$cartClass) {
            $cartClass = 'Vespolina\Entity\Order\Cart';
            $orderClass = 'Vespolina\Entity\Order\Order';
        }
        if (!$cartItemClass) {
            $cartItemClass = 'Vespolina\Entity\Order\Item';
        }
        if (!$cartEvents) {
            $cartEvents = 'Vespolina\Entity\Order\CartEvents';
        }
        if ($dispatcherClass) {
            $eventDispatcher = new $dispatcherClass();
        } else {
            $eventDispatcher = null;
        }

        return OrderTestsCommon::getOrderManager();
    }

    protected function persistNewCart()
    {

    }

    protected function persistNewCartable($name)
    {

    }
    protected function verifyPersistence($cart)
    {
        if (method_exists(self::$gateway, 'getLastCart')) {
            $lastCart = self::$gateway->getLastCart();
            $this->assertSame($lastCart->getId(), $cart->getId(), 'verify that the cart was persisted through the gateway');
        } else {
            $this->markTestIncomplete('the persistance through the gateway was not tested');
        }
    }
}

class TestDispatcher implements EventDispatcherInterface
{
    protected $lastEvent;
    protected $lastEventName;

    public function createEvent($subject = null)
    {
        $event = new Event($subject);

        return $event;
    }

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

class Event implements EventInterface
{
    protected $name;
    protected $subject;

    public function __construct($subject)
    {
        $this->subject = $subject;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getSubject()
    {
        return $this->subject;
    }
}
