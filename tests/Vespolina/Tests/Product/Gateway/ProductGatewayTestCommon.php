<?php

/**
 * (c) 2011 - ∞ Vespolina Project http://www.vespolina-project.org
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Vespolina\Tests\Product\Gateway;

use Vespolina\Product\Manager\ProductManager;
use Vespolina\Product\Specification\ProductSpecification;
use Vespolina\Product\Specification\TaxonomyNodeSpecification;
use Vespolina\Taxonomy\Gateway\TaxonomyMemoryGateway;
use Vespolina\Taxonomy\Manager\TaxonomyManager;

/**
 * @author Daniel Kucharski <daniel@xerias.be>
 */
abstract class ProductGatewayTestCommon extends \PHPUnit_Framework_TestCase
{
    protected $productGateway;
    protected $productManager;
    protected $taxonomyGateway;
    protected $taxonomyManager;
    protected $taxonomyRootNode;
    protected $taxonomyNodes;

    protected function createProducts($max = 10)
    {
        $manager = $this->getProductManager();
        $products = array();

        for ($i = 0; $i < $max; $i++) {
            $product = $manager->createProduct();
            $product->setName('product' . $i);

            //Attach each product to all known taxonomy nodes
            foreach ($this->taxonomyNodes as $node) {
                $product->addTaxonomy($node);
            }
            $products[] = $product;
        }

        return $products;
    }

    protected function setUp()
    {
        $this->setupTaxonomyNodes();
    }


    protected function setupTaxonomyNodes()
    {
        $taxonomyManager = $this->getTaxonomyManager();
        $this->taxonomyNodes = $taxonomyManager->matchAll(new TaxonomyNodeSpecification());

        if (null == $this->taxonomyNodes || count($this->taxonomyNodes) == 0) {

            $node1 = $taxonomyManager->createTaxonomyNode('category1', $this->taxonomyRootNode);
            $node2 = $taxonomyManager->createTaxonomyNode('category2', $this->taxonomyRootNode);
            $taxonomyManager->updateTaxonomyNode($node1);
            $taxonomyManager->updateTaxonomyNode($node2);

            $this->taxonomyGateway->flush();
            $this->taxonomyNodes = array($node1, $node2);
        }
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

    public function testMatchProductEquals()
    {
        $products = $this->createAndPersistProducts(10);

        $spec = new ProductSpecification();
        $spec->equals('name', 'product2');

        $product = $this->productGateway->findOne($spec);
        $this->assertNotNull($product);
        $this->assertEquals('product2', $product->getName());
    }

    public function testMatchProductById()
    {
        $products = $this->createAndPersistProducts();
        $targetProduct = $products[0];

        $product = $this->productGateway->matchProductById($targetProduct->getId());
        $this->assertNotNull($product);
        $this->assertEquals($targetProduct, $product);

        $this->assertNull($this->productGateway->matchProductById(100000000));
    }
    
    public function testMatchProductByTaxonomyNode()
    {
        $this->createAndPersistProducts(10);

        $matchingProductFound = false;
        $productSpec = new ProductSpecification();
        $productSpec->withTaxonomyNodeName('category1');

        $products = $this->productGateway->findAll($productSpec);

        foreach ($products as $product) {
            $occurs = false;

           //Test if the product has the request taxonomy node
           foreach($product->getTaxonomies() as $taxonomyNode) {
               if ($taxonomyNode->getName() == 'category1') $occurs = true;
           }
           $this->assertTrue($occurs);
           $matchingProductFound = true;
        }

        $this->assertTrue($matchingProductFound, 'oops');
    }

    public function testMatchProductByNotExistingTaxonomyNode()
    {
        $productSpec = new ProductSpecification();
        $productSpec->withTaxonomyNodeName('categoryXYZ');

        $products = $this->productGateway->findAll($productSpec);

        if (is_array($products)) {
            $count = count($products);
        } else {
            $count = $products->count();
        }
        $this->assertEquals(0, $count);
    }

    protected function createAndPersistProducts($count = 10)
    {
        $products = $this->createProducts($count);

        foreach ($products as $product) {
            $this->productGateway->updateProduct($product, false);
        }
        $this->productGateway->flush();

        return $products;
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