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
        $productIdentifiers = new \ReflectionProperty('Vespolina\ProductBundle\Model\Product', 'identifiers');
        $productIdentifiers->setAccessible(true);

        $identifierSets = $productIdentifiers->getValue($product);
        $this->assertSame(1, $identifierSets->count(), 'there should be a single default identifier set');
        $this->assertInstanceOf(
            'Doctrine\Common\Collections\ArrayCollection',
            $identifierSets,
            'the identifierSets should be stored in an ArrayCollection'
        );
        $this->assertTrue($identifierSets->containsKey('primary:primary;'), 'primary identifier should be set');

        $identifierSet = $product->getIdentifierSet();
        $this->assertSame(array('primary' => 'primary'), $identifierSet->getOptions(), 'no parameter passed to getIdentifierSet should return the primary identifier');

        $identifierSet->isActive(false);
        $this->assertFalse($identifierSet->isActive());

        $identifier = $this->createProductIdentifier('abc', 'blue');
        $this->setExpectedException('\Symfony\Component\DependencyInjection\Exception\ParameterNotFoundException');
        $product->addIdentifier($identifier, array('not' => 'available'));
    }

    public function testOptionIdentities()
    {
        $product = $this->createProduct();

        $pi = new \ReflectionProperty('Vespolina\ProductBundle\Model\Product', 'identifiers');
        $pi->setAccessible(true);

        $ogSize = $this->createOptionGroup('size');

        $ogSize->addOption($this->createOption('extra large', 'size', 'sizeXL'));
        $product->addOptionGroup($ogSize);

        $identifiers = $pi->getValue($product);

        $this->assertCount(1, $identifiers, 'you should have a sizeXL identifier');

        $sizeIdentifierSet = $identifiers->get('size:sizeXL;');
        $optionSet = $sizeIdentifierSet->getOptions();
        $this->assertInternalType('array', $optionSet);
        $this->assertArrayHasKey('size', $optionSet);
        $this->assertSame('sizeXL', $optionSet['size']);

        $product->clearOptions();
        $identifiers = $pi->getValue($product);
        $this->assertSame(0, $identifiers->count());

        $ogSize->addOption($this->createOption('large', 'size', 'sizeLG'));

        $ogColor = $this->createOptionGroup('color');
        $ogColor->addOption($this->createOption('red', 'color', 'colorRD'));
        $ogColor->addOption($this->createOption('blue', 'color', 'colorBL'));

        $options = array($ogSize, $ogColor);

        $product->setOptions($options);

        $identifiers = $pi->getValue($product);
        $this->assertSame(4, $identifiers->count());
        $this->assertTrue($identifiers->containsKey('color:colorBL;size:sizeLG;'));
        $this->assertTrue($identifiers->containsKey('color:colorBL;size:sizeXL;'));
        $this->assertTrue($identifiers->containsKey('color:colorRD;size:sizeLG;'));
        $this->assertTrue($identifiers->containsKey('color:colorRD;size:sizeXL;'));

        $largeBlue = $identifiers->get('color:colorBL;size:sizeLG;');
        $largeBlue->addIdentifier($this->createIdentifier('abc', '123'));
        $product->setOptions($options);
        $preservedIdentifiers = $product->getIdentifierSet(array('color' => 'colorBL', 'size' => 'sizeLG'));
        $this->assertContains('abc', $preservedIdentifiers->getIdentifierTypes(), 'existing options should be preserved when options are set (used by Form)');

        $ogMaterial = $this->createOptionGroup('material');
        $ogMaterial->addOption($this->createOption('iron', 'material', 'materialIron'));
        $ogMaterial->addOption($this->createOption('mithril', 'material', 'materialMithril'));

        $product->addOptionGroup($ogMaterial);

        $identifiers = $pi->getValue($product);
        $this->assertSame(8, $identifiers->count());
        $this->assertTrue($identifiers->containsKey('color:colorBL;material:materialIron;size:sizeLG;'));
        $this->assertTrue($identifiers->containsKey('color:colorBL;material:materialIron;size:sizeXL;'));
        $this->assertTrue($identifiers->containsKey('color:colorRD;material:materialIron;size:sizeLG;'));
        $this->assertTrue($identifiers->containsKey('color:colorRD;material:materialIron;size:sizeXL;'));
        $this->assertTrue($identifiers->containsKey('color:colorBL;material:materialMithril;size:sizeLG;'));
        $this->assertTrue($identifiers->containsKey('color:colorBL;material:materialMithril;size:sizeXL;'));
        $this->assertTrue($identifiers->containsKey('color:colorRD;material:materialMithril;size:sizeLG;'));
        $this->assertTrue($identifiers->containsKey('color:colorRD;material:materialMithril;size:sizeXL;'));

        $product->removeOptionGroup($ogMaterial);

        $identifiers = $pi->getValue($product);
        $this->assertSame(4, $identifiers->count(), 'removing the material options should make the number of identifier goes back');

        $ogSize->addOption($this->createOption('small', 'size', 'sizeSM'));

        $product->processIdentifiers();
        $identifiers = $pi->getValue($product);
        $this->assertSame(6, $identifiers->count(), 'add option to group outside of product will not take affect until processed');
        $this->assertTrue($identifiers->containsKey('color:colorBL;size:sizeSM;'));
        $this->assertTrue($identifiers->containsKey('color:colorBL;size:sizeLG;'));
        $this->assertTrue($identifiers->containsKey('color:colorBL;size:sizeXL;'));
        $this->assertTrue($identifiers->containsKey('color:colorRD;size:sizeSM;'));
        $this->assertTrue($identifiers->containsKey('color:colorRD;size:sizeLG;'));
        $this->assertTrue($identifiers->containsKey('color:colorRD;size:sizeXL;'));


        $optionIdentifierSet = $this->createProductIdentifierSet(array('color' => 'blue'));

        $identifiers = $pi->getValue($product);
        $identifiers->set('color:blue;', $optionIdentifierSet);

        $identifier = $this->createProductIdentifier('abc', 'blue');
        $product->addIdentifier($identifier, array('color' => 'blue'));

        $identifierSet = $product->getIdentifierSet(array('color' => 'blue'));

        $identifiers = $identifierSet->getIdentifiers();

        $this->assertSame(1, $identifiers->count());
        $this->assertSame('abc', $identifiers->first()->getName());
    }
}
