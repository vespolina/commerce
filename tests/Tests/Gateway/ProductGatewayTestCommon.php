<?php
namespace Tests\Gateway;

use Vespolina\Product\Manager\ProductManager;
use Vespolina\Product\Specification\ProductSpecification;

/**
 * (c) 2012 Vespolina Project http://www.vespolina-project.org
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */


/**
 * @author Daniel Kucharski <daniel@xerias.be>
 */
abstract class ProductGatewayTestCommon extends \PHPUnit_Framework_TestCase
{
    protected $gateway;

    protected function createProducts($max = 10)
    {
        $manager = $this->createProductManager();
        $products = array();

        for ($i = 0; $i < $max; $i++) {
            $product = $manager->createProduct();
            $product->setName('product' . $i);
            $products[] = $product;
        }

        return $products;
    }

    public function testCreateAndFindProducts()
    {
        $products = $this->createProducts(10);
        foreach ($products as $product) {
            $this->gateway->updateProduct($product);
        }

        /**
        foreach ($products as $product) {
            $productFound = $this->gateway->matchProductById($product->getId());
            $this->assertNotNull($productFound);
            $this->assertTrue($product->equals($productFound));
        } **/
    }

    public function testMatchProduct()
    {
        $products = $this->createProducts(10);
        foreach ($products as $product) {
            $this->gateway->updateProduct($product);
        }

        $spec = new ProductSpecification();
        $spec->equals('name', 'product2');

        $product = $this->gateway->matchProduct($spec);
        $this->assertNotNull($product);
        $this->assertEquals('product2', $product->getName());
    }

    protected function createProductManager()
    {
        return new ProductManager($this->gateway, array(
            'merchandiseClass' => 'Vespolina\Entity\Product\Merchandise',
            'attributeClass' => 'Vespolina\Entity\Product\Attribute',
            'optionClass' => 'Vespolina\Entity\Product\Option',
            'productClass' => 'Vespolina\Entity\Product\Product'
        ));
    }
}