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
use Vespolina\ProductBundle\Model\Node\ProductIdentifierSet;
use Vespolina\ProductBundle\Model\Node\IdentifierNode;
use Vespolina\ProductBundle\Model\Node\OptionSet;

/**
 * @author Richard D Shank <develop@zestic.com>
 */
class ProductTest extends WebTestCase
{
    public function testOptionSet()
    {
        $product = new Product(new OptionSet());

        $this->assertInstanceOf(
            'Vespolina\ProductBundle\Model\Node\OptionSetInterface',
            $product->getOptions(),
            'an empty class with OptionSetInterface should be set');

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
            'addOption hands option off to OptionsSet'
        );
    }

    public function testProductFeatures()
    {
        $product = new Product(new OptionSet());

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

    public function testProductIdentities()
    {
        $product = new Product(new OptionSet());

        $identifierSet = $this->createProductIdentifierSet('test123');

        $product->addIdentifierSet('test123', $identifierSet);
        $this->assertInstanceOf(
            'Doctrine\Common\Collections\ArrayCollection',
            $product->getIdentifiers(),
            'the identifiers should be stored in an ArrayCollection'
        );

        $this->assertSame(
            $identifierSet,
            $product->getIdentifierSet('test123'),
            'the identifier should be returned by the key'
        );

        $product = new Product(new OptionSet());

        $identifierSet = $this->createProductIdentifierSet('test123');

        $product->addIdentifierSet('test123', $identifierSet);

        $identifiers = array();

        $identifiers['abc'] = $this->createProductIdentifierSet('abc');
        $identifiers['123'] = $this->createProductIdentifierSet('123');
        
        $product->setIdentifiers($identifiers);
        $this->assertInstanceOf(
            'Doctrine\Common\Collections\ArrayCollection',
            $product->getIdentifiers(),
            'an array of IdentifierSets should be put into an ArrayCollection'
        );
        
        $this->assertEquals(
            2,
            $product->getIdentifiers()->count(),
            'any identifier sets already in product are removed when setIdentifiers is called'
        );
    }

    protected function createProductIdentifierSet($code)
    {
        $pi = new ProductIdentifierSet();
        $identifier = $this->getMock('Vespolina\ProductBundle\Model\Node\IdentifierNode', array('getCode', 'getName'));
        $identifier->expects($this->any())
             ->method('getCode')
             ->will($this->returnValue($code));
        $identifier->expects($this->any())
             ->method('getName')
             ->will($this->returnValue($code));
        $pi->addIdentifier($identifier);
        return $pi;
    }
}
