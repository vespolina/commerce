<?php
/**
 * (c) 2012 Vespolina Project http://www.vespolina-project.org
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */
namespace Vespolina\CartBundle\Tests\Model;

use Doctrine\Bundle\MongoDBBundle\Tests\TestCase;

use Vespolina\CartBundle\Tests\Fixtures\Document\Cartable;
use Vespolina\CartBundle\Tests\CartTestCommon;

/**
 * @author Richard D Shank <develop@zestic.com>
 */
class CartTest extends \PHPUnit_Framework_TestCase
{
    public function testTotalCartItems()
    {
        $this->markTestSkipped('this needs to be udpated for pricing');
        $cart = $this->createCart('testCart');
        $cartable1 = $this->createCartableItem('cartable1', 1);
        $this->addItemToCart($cart, $cartable1);

        $this->assertSame(1, $cart->getPricing()->get('total'));

        $cartable2 = $this->createCartableItem('cartable2', 2);
        $item = $this->addItemToCart($cart, $cartable2);
        $item->setQuantity(3);

        $this->getPricingProvider()->determineCartPrices($cart);

        $this->assertSame(7, $cart->getPricing()->get('total'));


        // todo: add taxes, discount, and shipping type item
    }

    public function testRemoveItemFromCart()
    {
        $this->markTestSkipped('this needs to be udpated for pricing');
        $cart = $this->createCart('testCart');
        $cartable1 = $this->createCartableItem('cartable1', 1);
        $item = $this->addItemToCart($cart, $cartable1);
        $item->setQuantity(3);

        $this->getPricingProvider()->determineCartPrices($cart);
        $this->assertSame(3, $cart->getPricing()->get('total'));
        $this->removeItemFromCart($cart, $item);
        $this->getPricingProvider()->determineCartPrices($cart);
        $this->assertSame(0, $cart->getPricing()->get('total'));

    }

    public function testRemovesOneUnitOfItemFromCart()
    {

        //$this->markTestIncomplete('We have removed item completely but not by quantity, next step is to write method to remove quantity');
    }
}
