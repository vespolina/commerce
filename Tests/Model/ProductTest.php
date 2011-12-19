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
        $this->assertInstanceOf('Doctrine\Common\Collections\Collection', $options, 'the options should be stored as a Doctrine collection');
        $this->assertTrue($options->contains($ogSize), 'the options should be stored in the collection');

        $product->removeOptionGroup('size');
        $options = $productOptions->getValue($product);
        $this->assertTrue($options->isEmpty(), 'nothing should be left');

        $product->addOptionGroup($ogSize);
        $ogColor = $this->createOptionGroup();
        $ogColor->setName('color');
        $product->addOptionGroup($ogColor);

        $options = $productOptions->getValue($product);
        $this->assertCount(2, $options);
        $this->assertTrue($options->contains($ogSize), 'the options should be stored in the collection');
        $this->assertTrue($options->contains($ogColor), 'the options should be stored in the collection');

        $product->clearOptions();
        $options = $productOptions->getValue($product);
        $this->assertTrue($options->isEmpty());

        $options = array($ogColor, $ogSize);
        $product->setOptions($options);

        $options = $productOptions->getValue($product);
        $this->assertCount(2, $options);
        $this->assertTrue($options->contains($ogSize), 'the options should be stored in the collection');
        $this->assertTrue($options->contains($ogColor), 'the options should be stored in the collection');
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
        $product = $this->createProduct();
        $identifierSets = $product->getIdentifierSets();
        $this->assertCount(1, $identifierSets, 'there should be a single default identifier set');
        $this->assertInstanceOf(
            'Doctrine\Common\Collections\ArrayCollection',
            $identifierSets,
            'the identifierSets should be stored in an ArrayCollection'
        );
        $identifierSet = $identifierSets->first();
        $this->assertSame('primary', $identifierSet->getType(), 'default identifier set should before the primary identifier');

        $identifierSet = $product->getIdentifierSet();
        $this->assertSame('primary', $identifierSet->getType(), 'no parameter passed to getIdentifierSet should return the primary identifier');

        $identifier = $this->createProductIdentifier('abc', '123');
        $product->addIdentifier($identifier);

        $optionIdentifierSet = $this->createProductIdentifierSet(array('color' => 'blue'));
        $rc = new \ReflectionClass($product);
        $rp = $rc->getProperty('identifiers');
        $rp->setAccessible(true);
        $identifiers = $rp->getValue($product);
        $identifiers->add($optionIdentifierSet);

        $identifier = $this->createIdentifier('abc', 'blue');
        $product->addIdentifier($identifier, array('color' => 'blue'));

        $identifierSet = $product->getIdentifierSet(array('color' => 'blue'));
        $identifiers = $identifierSet->getIdentifiers();
        $this->assertCount(2, $identifiers);
        foreach ($identifiers as $identifier) {
            if ($identifier->getType() != 'id' && $identifier->getType() != 'abc') {
                $this->fail('unknown identifier set type');
            }
        }

    }
}
