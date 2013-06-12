<?php
/**
 * (c) 2011-2012 Vespolina Project http://www.vespolina-project.org
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

use Vespolina\Entity\Product\Product;
use Vespolina\Product\Specification\ProductSpecification;
use Vespolina\Product\Gateway\ProductDoctrineMongoDBGateway;

/**
 * @author Daniel Kucharski <daniel@xerias.be>
 */
class ProductSpecificationTest extends \PHPUnit_Framework_TestCase
{

    protected $productGateway;

    public function testSpecification()
    {

        $aTaxonomyNode = new \Vespolina\Entity\Taxonomy\TaxonomyNode();

        $spec = new ProductSpecification();

        $spec->equals('name', 't-shirt')
             ->attributeContains('color', array('blue','white'))
             ->attributeEquals('brand', 'nike')
             ->withTaxonomyNode($aTaxonomyNode)
             ->withPriceRange('netValue', 20, 30);

        $this->productGateway->matchProducts($spec);


    }

    protected function setUp()
    {
        $doctrineODM =  \Doctrine\ODM\MongoDB\DocumentManager::create();

        $this->productGateway = new ProductDoctrineMongoDBGateway($doctrineODM, 'Vespolina\Entity\Product\Product');
    }
}
