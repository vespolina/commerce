<?php

/**
 * (c) 2011 - ∞ Vespolina Project http://www.vespolina-project.org
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Vespolina\Tests\Product\Manager;

use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

use Vespolina\Entity\Channel\Channel;
use Vespolina\Entity\Product\Product;
use Vespolina\Product\Manager\ProductManager;

/**
 * @author Richard D Shank <develop@zestic.com>
 */
class ProductManagerTest extends \PHPUnit_Framework_TestCase
{
    /** @var $mgr \Vespolina\Product\Manager\ProductManager */
    protected $mgr;

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

        $this->mgr->addMerchandiseHandler($this->getMock(
            'Vespolina\Product\Handler\MerchandiseHandler', null ,  array('Vespolina\Entity\Product\Merchandise')));


        $channel = new Channel();
        $channel->setName('webshop1');
        $this->assertInstanceOf('Vespolina\Entity\Product\MerchandiseInterface', $this->mgr->createMerchandise($product, $channel));
    }

    public function testSearchForProductByIdentifier()
    {
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );

        // search by identifier should return a product set up with the specific information for that identifier
        // full results flag returns the full data set for the product
    }

    public function testSetProductSKU()
    {
        $mgr = $this->createProductManager();
        $product = new Product();
        $sku = 'ABC12345';
        $mgr->setProductSKU($product, $sku);

        $identifiers = $product->getIdentifiers();
        $this->assertCount(1, $identifiers);
        $identifier = array_shift($identifiers);
        $this->assertInstanceOf('Vespolina\Entity\Identifier\SKUIdentifier', $identifier);
        $this->assertSame($sku, $identifier->getCode());
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
        $product = $this->mgr->createProduct();
        $label = $this->mgr->createAttribute('label', 'Joat Music');
        $this->mgr->addAttributeToProduct($product, $label);
        $this->assertCount(1, $product->getAttributes(), 'the attribute should be added');
        $this->assertSame($label, $product->getAttribute('label'), 'the original attribute should be returned');

        $format = array('format' => 'mp3');
        $this->mgr->addAttributeToProduct($product, $format);
        $this->assertCount(2, $product->getAttributes(), 'an array has been passed in should be added as an attribute');

        $formatAttribute = $product->getAttribute('format');
        $this->assertInstanceOf('Vespolina\Entity\Product\AttributeInterface', $formatAttribute, 'the array should be turned into an Attribute object');
        $this->assertSame('format', $formatAttribute->getType(), 'the type should be copied');
        $this->assertSame('mp3', $formatAttribute->getName(), 'the name should be copied');

        $attribute = 'genre';
        $value = 'rock';
        $this->mgr->addAttributeToProduct($product, $attribute, $value);
        $this->assertCount(3, $product->getAttributes(), 'an array has been passed in should be added as an attribute');

        $formatAttribute = $product->getAttribute('genre');
        $this->assertInstanceOf('Vespolina\Entity\Product\AttributeInterface', $formatAttribute, 'the array should be turned into an Attribute object');
        $this->assertSame('genre', $formatAttribute->getType(), 'the type should be copied');
        $this->assertSame('rock', $formatAttribute->getName(), 'the name should be copied');
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
            ->will($this->returnValue(new Product()));
        $productHandler->expects($this->any())
            ->method('getType')
            ->will($this->returnValue($type));

        return $productHandler;
    }

    protected function createProductManager()
    {
        $productGateway = $this->getMockBuilder('Vespolina\Product\Gateway\ProductGateway')
            ->disableOriginalConstructor()
            ->getMock()
        ;
        $mgr = new ProductManager($productGateway, array(
            'merchandiseClass' => 'Vespolina\Entity\Product\Merchandise',
            'attributeClass' => 'Vespolina\Entity\Product\Attribute',
            'optionClass' => 'Vespolina\Entity\Product\Option',
            'productClass' => 'Vespolina\Entity\Product\Product',
        ));

        return $mgr;
    }
}
