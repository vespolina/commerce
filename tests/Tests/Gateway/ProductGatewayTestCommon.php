<?php
namespace Tests\Gateway;

use Vespolina\Product\Manager\ProductManager;
use Vespolina\Product\Specification\ProductSpecification;
use Vespolina\Taxonomy\Gateway\TaxonomyMemoryGateway;
use Vespolina\Taxonomy\Manager\TaxonomyManager;

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
    protected $productGateway;
    protected $productManager;
    protected $taxonomyGateway;
    protected $taxonomyManager;

    protected function createProducts($max = 10)
    {
        $manager = $this->getProductManager();
        $taxonomyNodes = $this->createTaxonomyNodes();

        $products = array();

        for ($i = 0; $i < $max; $i++) {
            $product = $manager->createProduct();
            $product->setName('product' . $i);

            //Attach each product to all taxonomy nodes
            foreach ($taxonomyNodes as $node) {
                //$product->addTaxonomy($node);
            }

            $products[] = $product;
        }

        return $products;
    }

    protected function createTaxonomyNodes()
    {
        $taxonomyManager = $this->getTaxonomyManager();

        $node1 = $taxonomyManager->createTaxonomyNode('category1');
        $node2 = $taxonomyManager->createTaxonomyNode('category2');
        $taxonomyManager->updateTaxonomyNode($node1);
        $taxonomyManager->updateTaxonomyNode($node2);

        return array($node1, $node2);

    }

    public function testCreateAndFindProducts()
    {
        $products = $this->createProducts(10);

        foreach ($products as $product) {
            $this->productGateway->updateProduct($product, false);
        }
        $this->productGateway->flush();

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
            $this->productGateway->updateProduct($product);
        }

        $spec = new ProductSpecification();
        $spec->equals('name', 'product2');

        $product = $this->productGateway->matchProduct($spec);
        $this->assertNotNull($product);
        $this->assertEquals('product2', $product->getName());
    }

    public function testMatchProductById()
    {
        $products = $this->createProducts(10);
        foreach ($products as $product) {
            $this->productGateway->updateProduct($product);
        }

        $product = $this->productGateway->matchProductById(1);
        $this->assertNotNull($product);
    }

    protected function getProductManager()
    {
        if (null == $this->productManager) {
            $this->productManager =  new ProductManager($this->productGateway, array(
                'merchandiseClass' => 'Vespolina\Entity\Product\Merchandise',
                'attributeClass' => 'Vespolina\Entity\Product\Attribute',
                'optionClass' => 'Vespolina\Entity\Product\Option',
                'productClass' => 'Vespolina\Entity\Product\Product'
            ));
        }

        return $this->productManager;
    }

    protected function getTaxonomyManager()
    {
        if (null == $this->taxonomyManager) {
            $this->taxonomyManager = new TaxonomyManager(
                $this->taxonomyGateway,
                'Vespolina\Entity\Taxonomy\TaxonomyNode');
        }

        return $this->taxonomyManager;
    }
}