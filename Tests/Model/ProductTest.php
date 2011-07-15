<?php
/**
 * (c) Vespolina Project http://www.vespolina-project.org
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Vespolina\ProductBundle\Tests\Model;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

use Vespolina\ProductBundle\Model\Product;
use Vespolina\ProductBundle\Model\Node\ProductIdentifiers;
use Vespolina\ProductBundle\Model\Node\IdentifierNode;

/**
 * @author Richard D Shank <develop@zestic.com>
 */
class ProductTest extends WebTestCase
{
    public function testProduct()
    {
        $product = new Product();
        $this->assertInstanceOf(
            'Vespolina\ProductBundle\Model\Node\ProductFeaturesInterface',
            $product->getFeatures(),
            'an empty class with ProductFeaturesInterface should be set');
        $this->assertInstanceOf(
            'Vespolina\ProductBundle\Model\Node\ProductOptionsInterface',
            $product->getOptions(),
            'an empty class with ProductOptionsInterface should be set');

        $product->setPrimaryIdentifer('TestNode');
        $this->assertSame('TestNode', $product->getPrimaryIdentifer(), 'the identifier node can be set by string');

        $product->setPrimaryIdentifier(new IdentifierNode());
        $this->assertSame(
            'Vespolina\ProductBundle\Model\Node\IdentifierNode',
            $product->getPrimaryIdentifer(),
            'the identifier node can be set by instance'
        );

        $testSKU = $this->getMock('Vespolina\ProductBundle\Model\Node\IdentifierNodeId', array('getCode'));
        $testSKU->expects($this->any())
                 ->method('getCode')
                 ->will($this->returnValue('AB-CD-EF-GH'));

        $product->setPrimaryIdentifier($testSKU);
        $pi = new ProductIdentifiers();
        $pi->addIdentifier($testSKU);

        $productIdentifiers = new \ReflectionProperty('Vespolina\ProductBundle\Model\Product', 'identifiers');
        $productIdentifiers->setAccessible(true);

        $product->addIdentifier($pi);
        $this->assertArrayHasKey(
            'AB-CD-EF-GH',
            $productIdentifiers->getValue($product),
            'ProductIdentifiers should be indexed by the principle identifier type value'
        );

        $product = new Product();
        $this->setExpectedException('UnexpectedValueException', 'The primary identifier type has not been set');
        $product->addIdentifier($pi);
    }
}
