<?php
/**
 * (c) Vespolina Project http://www.vespolina-project.org
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */
namespace Vespolina\ProductBundle\Tests\Model;

use Vespolina\ProductBundle\Model\Product;
use Vespolina\ProductBundle\Model\Identifier\ProductIdentifierSet;
use Vespolina\ProductBundle\Model\Identifier\Identifier;
use Vespolina\ProductBundle\Tests\ProductTestCommon;

/**
 * @author Richard D Shank <develop@zestic.com>
 */
class ProductTest extends ProductTestCommon
{
    public function testOptionGroups()
    {
        $product = $this->createProduct();

        $productOptions = new \ReflectionProperty('Vespolina\ProductBundle\Model\Product', 'options');
        $productOptions->setAccessible(true);

        $ogSize = $this->createOptionGroup();
        $ogSize->setName('size');
        $product->addOptionGroup($ogSize);

        $options = $productOptions->getValue($product);
        $this->assertArrayHasKey('size', $options, 'the options should be stored with the type as the key');
        $this->assertSame($ogSize, $options['size']);

        $product->removeOptionGroup('size');
        $options = $productOptions->getValue($product);
        $this->assertEmpty($options, 'nothing should be left');

        $product->addOptionGroup($ogSize);
        $ogColor = $this->createOptionGroup();
        $ogColor->setName('color');
        $product->addOptionGroup($ogColor);

        $options = $productOptions->getValue($product);
        $this->assertCount(2, $options);
        $this->assertArrayHasKey('size', $options, 'the options should be stored with the type as the key');
        $this->assertArrayHasKey('color', $options, 'the options should be stored with the type as the key');

        $product->clearOptions();
        $options = $productOptions->getValue($product);
        $this->assertEmpty($options);

        $options = array($ogColor, $ogSize);
        $product->setOptionGroups($options);

        $options = $productOptions->getValue($product);
        $this->assertCount(2, $options);
        $this->assertArrayHasKey('size', $options, 'the options should be stored with the type as the key');
        $this->assertArrayHasKey('color', $options, 'the options should be stored with the type as the key');

        $productOptions = $product->getOptions();
        $this->assertSame($options, $productOptions);
    }

    public function testProductFeatures()
    {
        $product = $this->createProduct();

        $productFeatures = new \ReflectionProperty('Vespolina\ProductBundle\Model\Product', 'features');
        $productFeatures->setAccessible(true);

        $labelFeature = $this->getMock('Vespolina\ProductBundle\Model\Feature\Feature', array('getType', 'getSearchTerm'));
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
        $this->markTestSkipped('ProductIdentifierSet behavior has changed');

        $product = $this->createProduct();

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

        $product = $this->createProduct();

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
}
