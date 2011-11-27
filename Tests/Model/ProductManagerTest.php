<?php
/**
 * (c) Vespolina Project http://www.vespolina-project.org
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Vespolina\ProductBundle\Tests\Model;

use Symfony\Component\DependencyInjection\Container;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

use Vespolina\ProductBundle\Model\Product;
use Vespolina\ProductBundle\Model\Option\OptionSet;
use Vespolina\ProductBundle\Model\Identifier\ProductIdentifierSet;

/**
 * @author Richard D Shank <develop@zestic.com>
 */
class ProductManagerTest extends WebTestCase
{
    protected $mgr;
    protected $product;

    protected function setUp()
    {
        $this->mgr = $this->createProductManager('Vespolina\ProductBundle\Model\Identifier\IdIdentifier');
        $this->product = $this->createProduct('Vespolina\ProductBundle\Model\Product');
        //Product(new OptionSet())
    }

    public function testSearchForProductByIdentifier()
    {
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );

        // search by identifier should return a product set up with the specific information for that identifier
        // full results flag returns the full data set for the product
    }

    public function testCreateIdentifierSet()
    {
        $this->markTestIncomplete(
            'Behavior has changed, needs refactoring.'
        );

        $mgr = $this->createProductManager('Vespolina\ProductBundle\Model\Identifier\IdIdentifier');
        $this->assertInstanceOf(
            'Vespolina\ProductBundle\Model\Identifier\ProductIdentifierSet',
            $mgr->createIdentifierSet($this->createIdentifierNode('noset')),
            'using an instance of the primary identifier as a parameter should create a new PrimaryIdentifierSet'
        );
    }

    public function testCreateOption()
    {
<<<<<<< HEAD
        $mgr = $this->createProductManager('Vespolina\ProductBundle\Model\Identifier\IdIdentifier');
=======
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );

        $mgr = $this->createProductManager('Vespolina\ProductBundle\Model\Identifier\Identifier');
>>>>>>> 116e70a5cfb29515a3cc0c9af00b1e78b96fdce6

        $option = $mgr->createOption('CoLoR', 'BlAcK');

        $this->assertInstanceOf(
            'Vespolina\ProductBundle\Model\Option\OptionInterface',
            $option,
            'an Option instance should be created'
        );

        $this->assertEquals(
            'CoLoR',
            $option->getType(),
            'make sure the type of the option is stored correctly'
        );

        $this->assertEquals(
            'BlAcK',
            $option->getValue(),
            'make sure the value of the option is stored correctly'
        );
    }

    public function testAddFeatureToProduct()
    {
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );

        $label = $this->createFeature('label', 'Joat Music');

        $this->mgr->addFeatureToProduct($label, $this->product);
        $this->assertEquals(1, $this->product->getFeatures()->count(), 'make sure the feature has been added');
    }

    public function testGetImageManager()
    {
<<<<<<< HEAD
        $mediaManager = null;
        $mgr = $this->createProductManager('Vespolina\ProductBundle\Model\Identifier\IdIdentifier');

        // @todo commenting this because we are not passing the mediamanager on the createProductManager method yet
        //$this->assertSame($mediaManager, $mgr->getMediaManager());
=======
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
        $mgr = $this->createProductManager($mediaManager);

        $this->assertSame($mediaManager, $mgr->getMediaManager());
>>>>>>> 116e70a5cfb29515a3cc0c9af00b1e78b96fdce6

        $this->setExpectedException('Symfony\Component\Config\Definition\Exception\InvalidConfigurationException');
        $mgr = $this->createProductManager('Vespolina\ProductBundle\Model\Identifier\IdIdentifier');
        $mgr->getMediaManager();
    }

    public function testSearchForProductByFeature()
    {
        // search by identifier should return a product set up with the specific information for that identifier
        // full results flag returns the full data set for the product
    }

    public function testSearchForProductByFeatureType()
    {
        // search by identifier should return a product set up with the specific information for that identifier
        // full results flag returns the full data set for the product
    }

    protected function createProductManager($primaryIdentifier)
    {
        $mgr = $this->getMockBuilder('Vespolina\ProductBundle\Model\ProductManager')
            ->setMethods(array(
                '__construct',
                'createProduct',
                'findBy',
                'findProductById',
                'findProductByIdentifier',
                'getPrimaryIdentifier',
                'getIdentifierSetClass',
                'getOptionClass',
                'updateProduct'
            ))
             ->disableOriginalConstructor()
             ->getMock();
        $mgr->expects($this->any())
             ->method('getPrimaryIdentifier')
             ->will($this->returnValue($primaryIdentifier));
        $mgr->expects($this->any())
             ->method('getIdentifierSetClass')
             ->will($this->returnValue('Vespolina\ProductBundle\Document\ProductIdentifierSet'));
        $mgr->expects($this->any())
             ->method('getOptionClass')
             ->will($this->returnValue('Vespolina\ProductBundle\Document\Option'));
        return $mgr;
    }

    protected function createProduct($productAbstractClass)
    {
        $product = $this->getMockforAbstractClass($productAbstractClass);
        return $product;
    }

    protected function createProductIdentifiers($code)
    {
        $pi = $this->getMockforAbstractClass('Vespolina\ProductBundle\Model\Identifier\ProductIdentifierSet');

        $pi->addIdentifier($this->createIdentifierNode($code));
        return $pi;
    }

    protected function createIdentifierNode($code)
    {
        $identifier = $this->getMock('Vespolina\ProductBundle\Model\Identifier\IdIdentifier', array('getCode', 'getName'));
        $identifier->expects($this->any())
             ->method('getCode')
             ->will($this->returnValue($code));
        $identifier->expects($this->any())
             ->method('getName')
             ->will($this->returnValue($code));
        return $identifier;
    }

    protected function createFeature($type, $name)
    {
        $feature = $this->getMock('Vespolina\ProductBundle\Model\Feature\Feature', array('getType', 'getName', 'getSearchTerm'));
        $feature->expects($this->any())
             ->method('getType')
             ->will($this->returnValue($type));
        $feature->expects($this->any())
             ->method('getName')
             ->will($this->returnValue($name));
        $feature->expects($this->any())
             ->method('getSearchTerm')
             ->will($this->returnValue(strtolower($name)));
    }
}
