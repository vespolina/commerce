<?php

use Vespolina\Cart\Manager\CartManager;

class CartManagerTest extends \PHPUnit_Framework_TestCase
{
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

    protected function createCartManager()
    {

    }
}
