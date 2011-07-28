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


    }
}
