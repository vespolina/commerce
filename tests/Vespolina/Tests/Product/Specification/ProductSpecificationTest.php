<?php

/**
 * (c) 2011 - ∞ Vespolina Project http://www.vespolina-project.org
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Vespolina\Tests\Product\Specification;

use Doctrine\ODM\MongoDB\Mapping\Driver\XmlDriver;
use Doctrine\Common\Persistence\Mapping\Driver\SymfonyFileLocator;
use Vespolina\Entity\Brand\Brand;
use Vespolina\Entity\Channel\Channel;
use Vespolina\Entity\Product\Product;
use Vespolina\Product\Specification\ProductSpecification;
use Vespolina\Tests\Product\DoctrineODMTrait;

/**
 * @author Daniel Kucharski <daniel@xerias.be>
 */
class ProductSpecificationTest extends \PHPUnit_Framework_TestCase
{
    use \Vespolina\Tests\Product\DoctrineODMTrait;

    public function testProductSpecification()
    {
        $aTaxonomyNode = new \Vespolina\Entity\Taxonomy\TaxonomyNode();

        $spec = new ProductSpecification();
        $spec->equals('name', 't-shirt')
            ->attributeContains('color', array('blue', 'white'))
            ->attributeEquals('brand', 'nike')
            ->withTaxonomyNode($aTaxonomyNode)
            ->withPriceRange('netValue', 20, 30);

        $this->assertNotNull($spec);
    }

    public function testMerchandiseSpecification()
    {
        $storeChannel = new Channel();
        $spec = new ProductSpecification();
        $spec->equals('name', 't-shirt')
            ->withChannel($storeChannel);

    }

    public function testBrandSpecification()
    {
        $this->dbSetUp();
        $brand = new Brand();
        $brand->setName('brand');
        $this->brandGateway->persistBrand($brand);
        $newProduct = new Product();
        $newProduct->addBrand($brand);
        $this->productGateway->persistProduct($newProduct);
        $this->productGateway->flush();

        $spec = new ProductSpecification();
        $spec->withBrand($brand);

        $product = $this->productGateway->findOne($spec);
        $this->assertEquals($newProduct, $product);
    }
}
