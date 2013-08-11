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
class CartItemTest extends \PHPUnit_Framework_TestCase
{
    public function testTotalCartItems()
    {
        $this->markTestSkipped('this needs to be udpated for pricing');
        $cartable1 = $this->createCartableItem('cartable1', 1);
        $item = $this->createCartItem($cartable1);

        $this->getPricingProvider()->determineCartItemPrices($item);
        $this->assertSame(1, $item->getPricing()->get('total'), 'the price should be set cart item');

        $item->setQuantity(3);

        $this->getPricingProvider()->determineCartItemPrices($item);

        $this->assertSame(3, $item->getPricing()->get('total'), 'the price should change with a change in quantity');
    }
}
