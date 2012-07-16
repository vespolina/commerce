<?php

use Vespolina\Cart\Event\CartEvents;
use Vespolina\Cart\Manager\CartManager;
use Vespolina\Cart\Pricing\DefaultCartPricingProvider;
use Vespolina\Entity\Order\Cart;
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

        $this->markTestIncomplete('the pricing set should have been created'); // todo: not clear on behavior
        $this->markTestIncomplete('the cart should be persisted through the gateway');
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
            $cartItemClass = 'Vespolina\Entity\Order\CartItem';
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
