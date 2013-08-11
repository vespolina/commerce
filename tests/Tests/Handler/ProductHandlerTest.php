<?php

/**
 * (c) 2011 - ∞ Vespolina Project http://www.vespolina-project.org
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Tests\Handler;

use Vespolina\Product\Handler\ProductHandlerInterface;

class ProductHandlerTest extends \PHPUnit_Framework_TestCase
{
    protected $handler;

    protected function setUp()
    {
        // don't use the common handler, so the product creation and related functionality can be tested
        $this->handler = $this->getMock(
            'Vespolina\Product\Handler\ProductHandler',
            null,
            array('Vespolina\Entity\Product\Product')
        );
    }

    public function testCreateProduct()
    {
        $this->assertInstanceOf('Vespolina\Entity\Product\ProductInterface', $this->handler->createProduct());
    }

    public function testGetProductSearchData()
    {
        $product = $this->handler->createProduct();
        $product->setName('test');

        $searchData = $this->handler->getSearchTerms($product);

        $this->assertNotNull($searchData);
        $this->assertEquals($searchData['name'], $product->getName());
    }
}
