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

        $this->assertCount(2, $identifiers, 'you should have a primary identifier and a sizeXL identifier');

        $sizeIdentifierSet = $identifiers->get('size:sizeXL;');
        $optionSet = $sizeIdentifierSet->getOptionSet();
        $this->assertInternalType('array', $optionSet);
        $this->assertArrayHasKey('size');
        $this->assertSame('sizeXL', $optionSet['size']);

        $identifierTypes = $sizeIdentifierSet->getIdentifierTypes();
        $this->assertCount(1, $identifierTypes);
        $this->assertSame('_id', $identifierTypes[0]);

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
        $this->assertSame(5, $identifiers->count());
        $largeBlue = $identifiers->get('color:colorBL;size:sizeLG;');
        $this->assertInstanceOf('Vespolina\Model\Identifiers\ProductIdentifierSet', $largeBlue);
        $this->assertInstanceOf('Vespolina\Model\Identifiers\ProductIdentifierSet', $identifiers->get('color:colorBL;size:sizeXL;'));
        $this->assertInstanceOf('Vespolina\Model\Identifiers\ProductIdentifierSet', $identifiers->get('color:colorRD;size:sizeLG;'));
        $this->assertInstanceOf('Vespolina\Model\Identifiers\ProductIdentifierSet', $identifiers->get('color:colorRD;size:sizeXL;'));

        $largeBlue->addIdentifier($this->createIdentifier('abc', '123'));
        $product->setOptions($options);
        $preservedIdentifiers = $product->getIdentifier(array('color' => 'colorBL', 'size' => 'sizeXL'));
        $this->assertInArray('abc', $preservedIdentifiers->getIdentifierTypes(), 'existing options should be preserved when options are set (used by Form)');

        $ogMaterial = $this->createOptionGroup('material');
        $ogMaterial->addOption($this->createOption('iron', 'material', 'materialIron'));
        $ogMaterial->addOption($this->createOption('mithril', 'material', 'materialMithril'));

        $product->addOptionGroup($ogMaterial);

        $identifiers = $pi->getValue($product);
        $this->assertSame(9, $identifiers->count());
        $this->assertInstanceOf('Vespolina\Model\Identifiers\ProductIdentifierSet', $identifiers->get('color:colorBL;size:sizeLG;material:materialIron;'));
        $this->assertInstanceOf('Vespolina\Model\Identifiers\ProductIdentifierSet', $identifiers->get('color:colorBL;size:sizeXL;material:materialIron;'));
        $this->assertInstanceOf('Vespolina\Model\Identifiers\ProductIdentifierSet', $identifiers->get('color:colorRD;size:sizeLG;material:materialIron;'));
        $this->assertInstanceOf('Vespolina\Model\Identifiers\ProductIdentifierSet', $identifiers->get('color:colorRD;size:sizeXL;material:materialIron;'));
        $this->assertInstanceOf('Vespolina\Model\Identifiers\ProductIdentifierSet', $identifiers->get('color:colorBL;size:sizeLG;material:materialMithril;'));
        $this->assertInstanceOf('Vespolina\Model\Identifiers\ProductIdentifierSet', $identifiers->get('color:colorBL;size:sizeXL;material:materialMithril;'));
        $this->assertInstanceOf('Vespolina\Model\Identifiers\ProductIdentifierSet', $identifiers->get('color:colorRD;size:sizeLG;material:materialMithril;'));
        $this->assertInstanceOf('Vespolina\Model\Identifiers\ProductIdentifierSet', $identifiers->get('color:colorRD;size:sizeXL;material:materialMithril;'));

        $product->removeOptionGroup($ogMaterial);

        $identifiers = $pi->getValue($product);
        $this->assertSame(5, $identifiers->count(), 'removing the material options should make the number of identifier goes back');

        $ogSize->addOption($this->createOption('small', 'size', 'sizeSM'));

        $product->processIdentities();
        $identifiers = $pi->getValue($product);
        $this->assertSame(7, $identifiers->count(), 'add option to group outside of product will not take affect until processed');
        $this->assertInstanceOf('Vespolina\Model\Identifiers\ProductIdentifierSet', $identifiers->get('color:colorBL;size:sizeSM;'));
        $this->assertInstanceOf('Vespolina\Model\Identifiers\ProductIdentifierSet', $identifiers->get('color:colorBL;size:sizeLG;'));
        $this->assertInstanceOf('Vespolina\Model\Identifiers\ProductIdentifierSet', $identifiers->get('color:colorBL;size:sizeXL;'));
        $this->assertInstanceOf('Vespolina\Model\Identifiers\ProductIdentifierSet', $identifiers->get('color:colorRD;size:sizeSM;'));
        $this->assertInstanceOf('Vespolina\Model\Identifiers\ProductIdentifierSet', $identifiers->get('color:colorRD;size:sizeLG;'));
        $this->assertInstanceOf('Vespolina\Model\Identifiers\ProductIdentifierSet', $identifiers->get('color:colorRD;size:sizeXL;'));


        $optionIdentifierSet = $this->createProductIdentifierSet(array('color' => 'blue'));

        $identifiers = $pi->getValue($product);
        $identifiers->set('color:blue;', $optionIdentifierSet);

        $identifier = $this->createProductIdentifier('abc', 'blue');
        $product->addIdentifier($identifier, array('color' => 'blue'));

        $identifierSet = $product->getIdentifierSet(array('color' => 'blue'));

        $identifiers = $identifierSet->getIdentifiers();

        $this->assertSame(2, $identifiers->count());
        foreach ($identifiers as $identifier) {
            if ($identifier->getType() != 'id' && $identifier->getType() != 'abc') {
                $this->fail('unknown identifier set type');
            }
        }

    }
}
