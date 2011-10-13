<?php
/**
 * (c) Vespolina Project http://www.vespolina-project.org
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */
namespace Vespolina\ProductBundle\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * @author Richard D Shank <develop@zestic.com>
 */
abstract class ProductTestCommon extends WebTestCase
{
    protected function createProduct()
    {
        $product = $this->getMockForAbstractClass('Vespolina\ProductBundle\Model\Product');
        return $product;
    }

    protected function createProductIdentifierSet($code)
    {
        $pi = $this->getMockForAbstractClass('Vespolina\ProductBundle\Model\Identifier\ProductIdentifierSet');
        $identifier = $this->getMock('Vespolina\ProductBundle\Model\Identifier\BaseIdentifier', array('getCode', 'getName'));
        $identifier->expects($this->any())
             ->method('getCode')
             ->will($this->returnValue($code));
        $identifier->expects($this->any())
             ->method('getName')
             ->will($this->returnValue($code));
        $pi->addIdentifier($identifier);
        return $pi;
    }

    protected function createFeature()
    {
        return $this->getMockForAbstractClass('Vespolina\ProductBundle\Model\Feature\Feature');

    }
}
