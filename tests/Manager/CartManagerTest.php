<?php

use Vespolina\Cart\Manager\CartManager;
use Vespolina\Cart\Pricing\DefaultCartPricingProvider;

class CartManagerTest extends \PHPUnit_Framework_TestCase
{
    public function testConstruct()
    {
        $mgr = $this->createCartManager();
        $rp = new \ReflectionProperty($mgr, 'eventDispatcher');
        $rp->setAccessible(true);
        $dispatcher = $rp->getValue($mgr);
        $this->assertInstanceOf('Vespolina\EventDispatcher\NullDispatcher', $dispatcher, 'if a dispatcher is not passed set up the NullDispatcher');
    }

    public function testCreateCart()
    {
        $mgr = $this->createCartManager();
        $cart = $mgr->createCart('test');

        $this->assertInstanceOf('Vespolina\Entity\Cart', $cart, 'it should be an instance of the cart class passed in the construct');
        $this->assertSame('test', $cart->getName(), 'the name of cart should have been set when it was created');
        $this->markTestIncomplete('the cart should be persisted through the gateway');
        $this->markTestIncomplete('a CartEvents::INIT_ITEM event should be triggered');
        $this->markTestIncomplete('the pricing set should have been created');
        $this->asserSame(Cart::STATE_OPEN, $cart->getState());
    }

    protected function createCartManager($pricingProvider = null, $cartClass = null, $cartItemClass = null)
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

        return new CartManager($pricingProvider, $cartClass, $cartItemClass);
    }
}
