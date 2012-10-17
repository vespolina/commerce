<?php
/**
 * (c) 2011-2012 Vespolina Project http://www.vespolina-project.org
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

use Vespolina\Entity\Product\Product;
use Vespolina\Product\Manager\ProductManager;

/**
 * @author Richard D Shank <develop@zestic.com>
 */
class ProductManagerTest extends \PHPUnit_Framework_TestCase
{
    protected $mgr;
    protected $product;

    protected function setUp()
    {
        $this->mgr = $this->createProductManager();
    }

    public function testAddProductHandler()
    {
        $handler = $this->createProductHandler('test');
        $this->mgr->addProductHandler($handler);

        $this->assertSame($handler, $this->mgr->getProductHandler('test'), 'a handler should be returned by type');
        $this->assertTrue(is_array($this->mgr->getProductHandlers()));
        $this->assertContains($handler, $this->mgr->getProductHandlers(), 'return all of the handlers');

        $handler2 = $this->createProductHandler('test2');
        $this->mgr->addProductHandler($handler2);
        $this->assertContains($handler2, $this->mgr->getProductHandlers(), 'return all of the handlers');
        $this->assertCount(2, $this->mgr->getProductHandlers());

        $this->mgr->removeProductHandler('test2');
        $this->assertCount(1, $this->mgr->getProductHandlers(), 'there should now only be one handler');

        $this->mgr->removeProductHandler('test');
        $this->assertEmpty($this->mgr->getProductHandlers(), 'there should be no handlers after test has been removed');
    }

    public function testCreateProduct()
    {
        $handler = $this->createProductHandler('test');
        $this->mgr->addProductHandler($handler);

        $this->assertInstanceOf('Vespolina\Entity\Product\ProductInterface', $this->mgr->createProduct('test'));
    }

    public function testCreateMerchandise()
    {
        $handler = $this->createProductHandler('test');
        $this->mgr->addProductHandler($handler);
        $product = $this->mgr->createProduct('test');

        $this->assertInstanceOf('Vespolina\Entity\Product\MerchandiseInterface', $this->mgr->createMerchandise($product));
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
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );

        $mgr = $this->createProductManager('Vespolina\ProductBundle\Model\Identifier\Identifier');

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

    public function testCreateAttribute()
    {
        $label = $this->mgr->createAttribute('label', 'Joat Music');

        $this->assertSame('label', $label->getType(), 'the type should be copied');
        $this->assertSame('Joat Music', $label->getName(), 'the name should be copied');
    }

    public function testAddAttributeToProduct()
    {
        $label = $this->mgr->createAttribute('label', 'Joat Music');
        $this->mgr->addAttributeToProduct($label, $this->product);
        $this->assertCount(1, $this->product->getAttributes(), 'the attribute should be added');
        $this->assertSame($label, $this->product->getAttribute('label'), 'the original attribute should be returned');

        $format = array('format' => 'mp3');
        $this->mgr->addAttributeToProduct($format, $this->product);
        $this->assertCount(2, $this->product->getAttributes(), 'an array has been passed in should be added as an attribute');

        $formatAttribute = $this->product->getAttribute('format');
        $this->assertInstanceOf('Vespolina\Entity\Product\Product', $formatAttribute, 'the array should be turned into an Attribute object');
        $this->assertSame('format', $formatAttribute->getType(), 'the type should be copied');
        $this->assertSame('mp3', $formatAttribute->getName(), 'the name should be copied');
    }

    public function testGetImageManager()
    {
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
        $mgr = $this->createProductManager($mediaManager);

        $this->assertSame($mediaManager, $mgr->getMediaManager());

        $this->setExpectedException('Symfony\Component\Config\Definition\Exception\InvalidConfigurationException');
        $mgr = $this->createProductManager('Vespolina\ProductBundle\Model\Identifier\IdIdentifier');
        $mgr->getMediaManager();
    }

    public function testSearchForProductByAttribute()
    {
        // search by identifier should return a product set up with the specific information for that identifier
        // full results flag returns the full data set for the product
    }

    public function testSearchForProductByAttributeType()
    {
        // search by identifier should return a product set up with the specific information for that identifier
        // full results flag returns the full data set for the product
    }

    protected function createProductHandler($type, $productClass = 'Vespolina\Entity\Product\Product')
    {
        $productHandler = $this->getMock(
            'Vespolina\Product\Handler\ProductHandler',
            array('createProduct', 'getType'),
            array(),
            '',
            false
        );
        $productHandler->expects($this->any())
            ->method('createProduct')
            ->will($this->returnValue($this->getMock($productClass)));
        $productHandler->expects($this->any())
            ->method('getType')
            ->will($this->returnValue($type));

        return $productHandler;
    }

    protected function createProductManager()
    {
        $mgr = $this->getMockBuilder('Vespolina\ProductBundle\Model\ProductManager')
            ->setMethods(array(
                '__construct',
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
             ->method('getIdentifierSetClass')
             ->will($this->returnValue('Vespolina\ProductBundle\Document\ProductIdentifierSet'));
        $mgr->expects($this->any())
             ->method('getOptionClass')
             ->will($this->returnValue('Vespolina\ProductBundle\Document\Option'));

        $mgr = new ProductManager(array(), 'Error', 'Vespolina\Entity\Product\Merchandise', 'assetmgr');

        return $mgr;
    }
}
