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
        $product = $this->getMock('Vespolina\ProductBundle\Model\Product', array('createProductIdentifierSet'), array('ProductIdentifierSet'));
        $product->expects($this->any())
            ->method('createProductIdentifierSet')
            ->withAnyParameters()
            ->will($this->returnCallback(array($this, 'createProductIdentifierSetCallback')));

        $pis = $this->createProductIdentifierSet(array('primary' => 'primary'));

        $ip = new \ReflectionProperty('Vespolina\ProductBundle\Model\Product', 'identifiers');
        $ip->setAccessible(true);
        $identifiers = $ip->getValue($product);
        $identifiers->set('primary:primary;', $pis);
        $ip->setValue($product, $identifiers);

        return $product;
    }

    public function createProductIdentifierSetCallback()
    {
        $args = func_get_args();

        return $this->createProductIdentifierSet($args[0]);
    }

    protected function createProductIdentifierSet($options)
    {
        $pis = $this->getMock('Vespolina\ProductBundle\Model\Identifier\ProductIdentifierSet', null, array($options), '', false);

        $op = new \ReflectionProperty('Vespolina\ProductBundle\Model\Identifier\ProductIdentifierSet', 'options');
        $op->setAccessible(true);
        $op->setValue($pis, $options);

        return $pis;
    }

    protected function createProductIdentifier($type, $code)
    {
        $bi = $this->getMock('Vespolina\ProductBundle\Model\Identifier\BaseIdentifier', array('getName'));
        $bi->expects($this->any())
            ->method('getName')
            ->will($this->returnValue($type));
        $bi->setCode($code);

        return $bi;
    }

    protected function createOption($display, $type, $value)
    {
        $option = $this->getMockForAbstractClass('Vespolina\ProductBundle\Model\Option\Option');

        $option->setType($type);
        $option->setDisplay($display);
        $option->setValue($value);

        return $option;
    }

    protected function createOptionGroup($name = null)
    {
        $og = $this->getMockForAbstractClass('Vespolina\ProductBundle\Model\Option\OptionGroup');
        if ($name) {
            $og->setName($name);
        }
        return $og;
    }
}
