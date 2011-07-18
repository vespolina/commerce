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

        /* options */
        $this->assertInstanceOf(
            'Vespolina\ProductBundle\Model\Node\ProductOptionsInterface',
            $product->getOptions(),
            'an empty class with ProductOptionsInterface should be set');

        $sizeLgOption = $this->getMock('Vespolina\ProductBundle\Model\Node\OptionNode', array('getType', 'getValue'));
        $sizeLgOption->expects($this->any())
                 ->method('getType')
                 ->will($this->returnValue('size'));
        $sizeLgOption->expects($this->any())
                 ->method('getValue')
                 ->will($this->returnValue('large'));
        $product->addOption($sizeLgOption);
        $this->assertSame(
            $sizeLgOption,
            $product->getOptions()->getOption('size', 'large'),
            'addOption hands option off to ProductOptions'
        );

        /* product identifiers */
        $product->setPrimaryIdentifier('\Vespolina\ProductBundle\Model\Node\IdentifierNode');
        $this->assertSame(
            '\Vespolina\ProductBundle\Model\Node\IdentifierNode',
            $product->getPrimaryIdentifier(),
            'the primary identifier node can be set by string'
        );

        $product->setPrimaryIdentifier('Vespolina\ProductBundle\Model\Node\IdentifierNode');
        $this->assertSame(
            '\Vespolina\ProductBundle\Model\Node\IdentifierNode',
            $product->getPrimaryIdentifier(),
            "the primary identifier class name must have a leading \\"
        );

        $product->setPrimaryIdentifier(new IdentifierNode());
        $this->assertSame(
            '\Vespolina\ProductBundle\Model\Node\IdentifierNode',
            $product->getPrimaryIdentifier(),
            'the primary identifier node can be set by instance'
        );

        $testSKU = $this->getMock('Vespolina\ProductBundle\Model\Node\IdentifierNode', array('getCode', 'getName'));
        $testSKU->expects($this->any())
                 ->method('getCode')
                 ->will($this->returnValue('AB-CD-EF-GH'));
        $testSKU->expects($this->any())
                 ->method('getName')
                 ->will($this->returnValue('AB-CD-EF-GH'));

        $product->setPrimaryIdentifier($testSKU);

        $productIdentifiers = new \ReflectionProperty('Vespolina\ProductBundle\Model\Product', 'identifiers');
        $productIdentifiers->setAccessible(true);

        $pi = new ProductIdentifiers();
        $pi->addIdentifier($testSKU);

        $product->addIdentifier($pi);
        $this->assertArrayHasKey(
            'AB-CD-EF-GH',
            $productIdentifiers->getValue($product),
            'ProductIdentifiers should be indexed by the principle identifier type value'
        );

        /* product features */
        $productFeatures = new \ReflectionProperty('Vespolina\ProductBundle\Model\Product', 'features');
        $productFeatures->setAccessible(true);

        $labelFeature = $this->getMock('Vespolina\ProductBundle\Model\Node\FeatureNode', array('getType', 'getSearchTerm'));
        $labelFeature->expects($this->any())
                 ->method('getType')
                 ->will($this->returnValue('LABEL'));
        $labelFeature->expects($this->any())
                 ->method('getSearchTerm')
                 ->will($this->returnValue('Joat Music'));

        $product->addFeature($labelFeature);
        $features = $productFeatures->getValue($product);
        $this->assertArrayHasKey('label', $features, 'top level key is the type in lower case');
        $this->assertArrayHasKey('joat music', $features['label'], 'top level key is the search term in lower case');

        /* exceptions */
        $product = new Product();
        $this->setExpectedException('UnexpectedValueException', 'The primary identifier type has not been set');
        $product->addIdentifier($pi);

        $this->setExpectedException(
            'InvalidArgumentException',
            'The primary identifier must be a string or an instance of Vespolina\ProductBundle\Node\IdentifierNodeInterface'
        );
        $product->setPrimaryIdentifier(new Product());

        $this->setExpectedException(
            'InvalidArgumentException',
            'The primary identifier must be an instance of Vespolina\ProductBundle\Node\IdentifierNodeInterface'
        );
        $product->setPrimaryIdentifier('Vespolina\ProductBundle\Model\Product');

        $product->setPrimaryIdentifier($testSKU);
        $this->setExpectedException(
            'UnexpectedValueException',
            'The primary identifier is not in this Vespolina\ProductBundle\Node\ProductIdentifiers instance'
        );
        $product->addIdentifier(new ProductIndentifiers());
    }
}
