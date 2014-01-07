<?php

use Vespolina\Entity\Partner\Partner;
use Vespolina\Entity\Product\Product;
use Vespolina\Entity\Order\Order;
use Vespolina\Entity\Order\OrderState;
use Vespolina\EventDispatcher\EventDispatcherInterface;
use Vespolina\EventDispatcher\EventInterface;
use Vespolina\Order\Gateway\OrderMemoryGateway;
use Vespolina\Order\Manager\OrderManager;
use Vespolina\Order\Pricing\DefaultOrderPricingProvider;
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

    public function testCreateOrder()
    {
        $mgr = $this->createOrderManager();
        $order = $mgr->createOrder('test');

        $this->assertInstanceOf('Vespolina\Entity\Order\Order', $order, 'it should be an instance of the order class passed in the construct');
        $this->assertSame('test', $order->getName(), 'the name of order should have been set when it was created');
        $this->assertSame(OrderState::OPEN, $order->getState());

        //$this->assertSame(OrderEvents::INIT_ORDER, $mgr->getEventDispatcher()->getLastEventName(), 'a OrderEvents::INIT_CART event should be triggered');
        //$event = $mgr->getEventDispatcher()->getLastEvent();
        //$this->assertInstanceOf('Vespolina\Entity\Order\OrderInterface', $event->getSubject());

        $this->verifyPersistence($order);
    }

    public function testFindOrderById()
    {
        $mgr = $this->createOrderManager();

        $order1 = $mgr->createOrder('findOrder1');
        $order2 = $mgr->createOrder('findOrder2');

        $orderId = $order1->getId();

        $loadedOrder = $mgr->findOrderById($orderId);

        $this->assertInstanceOf('Vespolina\Entity\Order\Order', $loadedOrder);
        $this->assertSame($order1, $loadedOrder);
    }

    public function testAddProductToOrder()
    {
        $mgr = $this->createOrderManager();
        $order = $mgr->createOrder('test');

        $product = new Product();
        $product->setName('test product');
        $product->setPrice(10);

        $mgr->addProductToOrder($order, $product);

        $items = $order->getItems();
        $this->assertSame(1, count($items));
        $item = $items[0];
        $this->assertSame($product, $item->getProduct());
        $this->assertSame(1, $item->getQuantity());
        $this->assertSame(10, $item->getPrice());
        $this->assertSame(10, $item->getPrice('subtotal'));
        $this->assertSame('test product', $item->getName());
        $this->assertSame(10, $order->getPrice());

        // add the same product again to increase the quantity
        $existingItem = $mgr->addProductToOrder($order, $product);
        $this->assertSame($existingItem, $item);
        $items = $order->getItems();
        $this->assertSame(1, count($items));
        $this->assertSame(2, $existingItem->getQuantity());
        $this->assertSame(20, $item->getPrice('subtotal'));
        $this->assertSame(20, $order->getPrice());

        //$this->assertSame(OrderEvents::UPDATE_ITEM, $mgr->getEventDispatcher()->getLastEventName(), 'a OrderEvents::UPDATE_ITEM event should be triggered');
        //$event = $mgr->getEventDispatcher()->getLastEvent();
        //$this->assertInstanceOf('Vespolina\Entity\Order\ItemInterface', $event->getSubject());

        // specifiy the quantity when adding a product to the order
        $mgr->addProductToOrder($order, $product, array(), 2);
        $this->assertSame(4, $existingItem->getQuantity(), 'passing the quantity should add to the existing quantity');
        $this->assertSame(40, $item->getPrice('subtotal'));
        $this->assertSame(40, $order->getPrice());

        //$this->assertSame(OrderEvents::UPDATE_ITEM, $mgr->getEventDispatcher()->getLastEventName(), 'a OrderEvents::UPDATE_ITEM event should be triggered');
        //$event = $mgr->getEventDispatcher()->getLastEvent();
        //$this->assertInstanceOf('Vespolina\Entity\Order\ItemInterface', $event->getSubject());

        $optionSet1 = array('color' => 'blue', 'size' => 'small');
        $optionSet2 = array('color' => 'red', 'size' => 'small');

        $option1Item = $mgr->addProductToOrder($order, $product, $optionSet1);
        $this->assertNotSame($option1Item, $existingItem, 'different options for the same product should be different items');
        $items = $order->getItems();
        $this->assertSame(2, count($items));

        $option2Item = $mgr->addProductToOrder($order, $product, $optionSet2, 3);
        $this->assertNotSame($option1Item, $option2Item, 'different options for the same product should be different items');
        $items = $order->getItems();
        $this->assertSame(3, count($items));
        $this->assertSame(3, $option2Item->getQuantity());

        //$this->assertSame(OrderEvents::INIT_ITEM, $mgr->getEventDispatcher()->getLastEventName(), 'a OrderEvents::INIT_ITEM event should be triggered');
        //$event = $mgr->getEventDispatcher()->getLastEvent();
        //$this->assertInstanceOf('Vespolina\Entity\Order\ItemInterface', $event->getSubject());
    }

    public function testAddProductToOrderDeferred()
    {
        $this->markTestIncomplete('copy testAddProductToOrder, but throw defer flag');
    }

    public function testFindProductInOrder()
    {
        $mgr = $this->createOrderManager();
        $order = $mgr->createOrder('test');

        $createItem = new \ReflectionMethod($mgr, 'createItem');
        $createItem->setAccessible(true);
        $addItem = new \ReflectionMethod($order, 'addItem');
        $addItem->setAccessible(true);

        $product = new Product();
        $product->setName('test product');
        $testItem = $createItem->invokeArgs($mgr, array($product));
        $addItem->invokeArgs($order, array($testItem));

        $item = $mgr->findProductInOrder($order, $product);
        $this->assertSame($product, $item->getProduct(), 'find the item that contains the product');

        $newProduct = new Product();
        $newProduct->setName('with options');
        $optionsBlue = array('color' => 'blue', 'size' => 'small');
        $blueItem = $createItem->invokeArgs($mgr, array($newProduct, $optionsBlue));
        $addItem->invokeArgs($order, array($blueItem));

        $foundBlueItem = $mgr->findProductInOrder($order, $newProduct, $optionsBlue);
        $this->assertSame($newProduct, $foundBlueItem->getProduct(), 'find the item that contains the product with the options');
        $this->assertSame($optionsBlue, $foundBlueItem->getOptions(), 'find the item that contains the product with the options');

        $optionsRed = array('color' => 'red', 'size' => 'large');
        $redItem = $createItem->invokeArgs($mgr, array($newProduct, $optionsRed));
        $addItem->invokeArgs($order, array($redItem));

        $foundRedItem = $mgr->findProductInOrder($order, $newProduct, array('size' => 'large', 'color' => 'red'));
        $this->assertNotSame($redItem, $blueItem);
        $this->assertSame($newProduct, $foundRedItem->getProduct(), 'find the item that contains the product with the options');
        $this->assertSame($optionsRed, $foundRedItem->getOptions(), 'find the item that contains the product with the options');

        $this->markTestIncomplete('this needed to be revisited');

        $this->assertNull($mgr->findProductInOrder($order, $product, $optionsRed), "product and options don't match, nothing returned");
        $this->assertNull($mgr->findProductInOrder($order, $newProduct), 'this item has options, so nothing returned');
        $this->assertNull($mgr->findProductInOrder($order, $newProduct, array('color' => 'yellow')), 'no yellow options set');
    }


    public function testFindOpenOrderByOwner()
    {
        $this->markTestSkipped("this needs to be a functional test, it is db dependent");
        $owner = new Partner('person');
        $mgr = $this->createOrderManager();

        $this->dm->persist($owner);
        $this->dm->flush();

        $order = $mgr->createOrder();
        $order->setOwner($owner);
        $mgr->updateOrder($order);

        $ownersOrder = $mgr->findOpenOrderByOwner($owner);
        $this->assertSame($order->getId(), $ownersOrder->getId());

        $mgr->setOrderState($order, Order::STATE_CLOSED);
        $this->assertNull($mgr->findOpenOrderByOwner($owner));

        return $order;
    }

    public function testGetActiveOrderForOwner()
    {
        $this->markTestSkipped("this needs to be a functional test, it is db dependent");
        $mgr = $this->createOrderManager();
        $owner = new Partner('person');

        $this->dm->persist($owner);
        $this->dm->flush();

        $session = $this->container->get('session');
        // not really a test, but it does make sure we start empty
        $this->assertNull($session->get('vespolina_order'));

        $firstPassOrder = $mgr->getActiveOrder($owner);
        $persistedOrders = $mgr->findBy(array());
        $this->assertSame(1, $persistedOrders->count(), 'there should only be one order in the db');
        $this->assertSame($firstPassOrder, $session->get('vespolina_order'), 'the new order should have been set for the session');

        $secondPassOrder = $mgr->getActiveOrder($owner);
        $this->assertSame($firstPassOrder->getId(), $secondPassOrder->getId());
        $this->assertSame(1, $persistedOrders->count(), 'there should only be one order in the db');

        $session->clear('vespolina_order');
        $thirdPassOrder = $mgr->getActiveOrder($owner);
        $this->assertSame($firstPassOrder->getId(), $thirdPassOrder->getId());
        $this->assertSame(1, $persistedOrders->count(), 'there should only be one order in the db');
        $this->assertSame($thirdPassOrder, $session->get('vespolina_order'), 'the new order should have been set for the session');
    }


    public function testGetActiveOrderWithoutOwner()
    {
        $this->markTestSkipped("this needs to be a functional test, it is db dependent");
        $mgr = $this->createOrderManager();
        $session = $this->container->get('session');
        // not really a test, but it does make sure we start empty
        $this->assertNull($session->get('vespolina_order'));

        $firstPassOrder = $mgr->getActiveOrder();
        $persistedOrders = $mgr->findBy(array());
        $this->assertSame(1, $persistedOrders->count(), 'there should only be one order in the db');
        $this->assertSame($firstPassOrder, $session->get('vespolina_order'), 'the new order should have been set for the session');

        $secondPassOrder = $mgr->getActiveOrder();
        $this->assertSame($firstPassOrder->getId(), $secondPassOrder->getId());
        $this->assertSame(1, $persistedOrders->count(), 'there should only be one order in the db');

        $session->clear('vespolina_order');
        $thirdPassOrder = $mgr->getActiveOrder();
        $this->assertNotSame($firstPassOrder->getId(), $thirdPassOrder->getId());
        $this->assertSame(2, $persistedOrders->count(), 'there is a left over order, this should probably be handled');
        $this->assertSame($thirdPassOrder, $session->get('vespolina_order'), 'the new order should have been set for the session');
    }

    public function testIsValidOpenOrder()
    {
        $mgr = $this->createOrderManager();

        $order = null;
        $this->assertFalse($mgr->isValidOpenOrder($order), 'a null for an order should return false');
        $order = new Order();
        $customer = new Partner();
        $customer->setName('Valid Customer');
        $order->setCustomer($customer);
        $wrongCustomer = new Partner();
        $wrongCustomer->setName('Wrong Customer');
        $this->assertFalse($mgr->isValidOpenOrder($order, $wrongCustomer), 'a passed customer must not matching the customer in the order should return false');
        $order->setState(OrderState::LOCKED);
        $this->assertFalse($mgr->isValidOpenOrder($order), 'an order not in an open state should return false');
        $order->setState(OrderState::OPEN);
        $this->assertTrue($mgr->isValidOpenOrder($order), 'an order meeting all of the conditions should return true');
        $this->assertTrue($mgr->isValidOpenOrder($order, $customer), 'an order with customer meeting all of the conditions should return true');
    }

    public function testRemoveProductFromOrder()
    {
        $mgr = $this->createOrderManager();
        $order = $mgr->createOrder();

        $product = new Product();
        $options = array('size' => 'large');

        $item = $mgr->addProductToOrder($order, $product);
        $this->assertCount(1, $order->getItems(), 'verify item is in order');
        $mgr->removeProductFromOrder($order, $product, $options);
        $this->assertContains($item, $order->getItems(), "the items should still be in the order since the item didn't have options");
        $mgr->removeProductFromOrder($order, $product);
        $this->assertEmpty($order->getItems(), 'the order should be empty again');

        $mgr->addProductToOrder($order, $product, $options);
        $mgr->removeProductFromOrder($order, $product, $options);
        $this->assertEmpty($order->getItems(), 'removing product with options');

        $item = $mgr->addProductToOrder($order, $product, $options);
        $mgr->removeProductFromOrder($order, $product);
        $this->assertContains($item, $order->getItems(), 'the items should still be in the order since options were not passed');
        $mgr->removeProductFromOrder($order, $product, array('size' => 'small'));
        $this->assertContains($item, $order->getItems(), 'the items should still be in the order since the wrong options were passed');

    }

    public function testSetOrderState()
    {
        $this->markTestSkipped("this needs to be rewritten w/o persistence dependence and test events");
        $mgr = $this->createOrderManager();
        $order = $this->persistNewOrder();

        $persistedOrder = $mgr->findOrderById($order->getId());
        $this->assertSame(OrderState::OPEN, $persistedOrder->getState(), 'the order should start in an open state');

        $mgr->setOrderState($order, 'close');
        $persistedOrder = $mgr->findOrderById($order->getId());
// there is a bug in mongodb will put in PR -- rds note from CartManager test, not sure what it means
        $this->assertSame('close', $persistedOrder->getState());
    }

    public function testSetOrderItemState()
    {
        $mgr = $this->createOrderManager();
        $order = $mgr->createOrder();
        $product = new Product();

        $item = $mgr->addProductToOrder($order, $product);

        $this->assertNotSame('test', $item->getState(), "make sure the state isn't set to test");
        $mgr->setOrderItemState($item, 'test');
        $this->assertSame('test', $item->getState(), "the state should now be set to test");

        //$this->assertSame(OrderEvents::UPDATE_ITEM_STATE, $mgr->getEventDispatcher()->getLastEventName(), 'a OrderEvents::UPDATE_ITEM_STATE event should be triggered');
        //$event = $mgr->getEventDispatcher()->getLastEvent();
        //$this->assertInstanceOf('Vespolina\Entity\Order\ItemInterface', $event->getSubject());
    }

    public function testSetItemQuantity()
    {
        $mgr = $this->createOrderManager();
        $order = $mgr->createOrder();

        $product = new Product();
        $item = $mgr->addProductToOrder($order, $product);

        $mgr->setItemQuantity($item, 5);
        $this->assertSame(5, $item->getQuantity(), 'the quantity should be updated');

        //$this->assertSame(OrderEvents::UPDATE_ITEM, $mgr->getEventDispatcher()->getLastEventName(), 'a OrderEvents::UPDATE_ITEM event should be triggered');
        //$event = $mgr->getEventDispatcher()->getLastEvent();
        //$this->assertInstanceOf('Vespolina\Entity\Order\ItemInterface', $event->getSubject());
    }

    public function testSetProductQuantity()
    {
        $mgr = $this->createOrderManager();
        $order = $mgr->createOrder();

        $product = new Product();
        $item = $mgr->addProductToOrder($order, $product);
        $mgr->setProductQuantity($order, $product, array(), 5);
        $this->assertSame(5, $item->getQuantity(), 'the quantity should be updated');
        $options = array('size' => 'large');
        $optionItem = $mgr->addProductToOrder($order, $product, $options);
        $mgr->setProductQuantity($order, $product, $options, 5);
        $this->assertSame(5, $optionItem->getQuantity(), 'the quantity should be updated');

        //$this->assertSame(OrderEvents::UPDATE_ITEM, $mgr->getEventDispatcher()->getLastEventName(), 'a OrderEvents::UPDATE_ITEM event should be triggered');
        //$event = $mgr->getEventDispatcher()->getLastEvent();
        //$this->assertInstanceOf('Vespolina\Entity\Order\ItemInterface', $event->getSubject());
    }

    public function testUpdateOrder()
    {
        $mgr = $this->createOrderManager();
        $order = $mgr->createOrder('testupdateOrder');
        $dummyOrder = $mgr->createOrder('toMakeSureTestupdateOrderIsNotLastOrder');

        $mgr->updateOrder($order, false);

        //$this->assertSame(OrderEvents::UPDATE_CART, $mgr->getEventDispatcher()->getLastEventName(), 'a OrderEvents::UPDATE_CART event should be triggered');
        //$event = $mgr->getEventDispatcher()->getLastEvent();
        //$this->assertInstanceOf('Vespolina\Entity\Order\OrderInterface', $event->getSubject());

        $this->verifyPersistence($dummyOrder); // should still be dummy order since persist parameter was false

       $mgr->createOrder('toMakeSureLastEventIsCreate');

        $mgr->updateOrder($order);
        //$this->assertSame(OrderEvents::UPDATE_CART, $mgr->getEventDispatcher()->getLastEventName(), 'a OrderEvents::UPDATE_CART event should be triggered');
        //$event = $mgr->getEventDispatcher()->getLastEvent();
        //$this->assertInstanceOf('Vespolina\Entity\Order\OrderInterface', $event->getSubject());

        $this->verifyPersistence($order);
    }

    protected function createOrderManager($gateway = null, $orderClass = null, $orderItemClass = null, $orderEvents = null, $dispatcherClass = 'TestDispatcher')
    {
        if (!$gateway) {
            $gateway = self::$gateway;
        }

        if (!$orderClass) {
            $orderClass = 'Vespolina\Entity\Order\Order';
        }
        if (!$orderItemClass) {
            $orderItemClass = 'Vespolina\Entity\Order\Item';
        }
        if ($dispatcherClass) {
            $eventDispatcher = new $dispatcherClass();
        } else {
            $eventDispatcher = null;
        }

        return OrderTestsCommon::getOrderManager();
    }

    protected function persistNewOrder()
    {

    }

    protected function persistNewOrderable($name)
    {

    }
    protected function verifyPersistence($order)
    {
        if (method_exists(self::$gateway, 'getLastOrder')) {
            $lastOrder = self::$gateway->getLastOrder();
            $this->assertSame($lastOrder->getId(), $order->getId(), 'verify that the order was persisted through the gateway');
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
