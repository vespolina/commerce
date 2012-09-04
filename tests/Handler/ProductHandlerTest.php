<?php
/**
 * (c) 2012 Vespolina Project http://www.vespolina-project.org
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

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
}
