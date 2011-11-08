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
    protected function createFeature()
    {
        return $this->getMockForAbstractClass('Vespolina\ProductBundle\Model\Feature\Feature');

    }

    protected function createIdentifier($name, $code)
    {
        $identifier = $this->getMock(
            'Vespolina\ProductBundle\Model\Identifier\BaseIdentifier',
            array('getName', 'getCode')
        );

        $identifier->expects($this->any())
            ->method('getName')
            ->will($this->returnValue($name));
        $identifier->expects($this->any())
            ->method('getCode')
            ->will($this->returnValue($code));

        return $identifier;
    }

    protected function createProduct()
    {
        $product = $this->getMockForAbstractClass('Vespolina\ProductBundle\Model\Product');
        return $product;
    }
    
    protected function createProductIdentifierSet()
    {
        $pis = $this->getMock('Vespolina\ProductBundle\Model\Identifier\ProductIdentifierSet', null, array(), '', false);

        return $pis;
    }

    protected function createOption($type, $value)
    {
        $option = $this->getMock(
            'Vespolina\ProductBundle\Model\Option\Option',
            array('getType', 'getValue'),
            array('Vespolina\ProductBundle\Model\Option\OptionsSet')
        );
        $option->expects($this->any())
             ->method('getType')
             ->will($this->returnValue($type));
        $option->expects($this->any())
             ->method('getValue')
             ->will($this->returnValue($value));

        return $option;
    }
}
