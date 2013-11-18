<?php

/**
 * (c) 2011 - ∞ Vespolina Project http://www.vespolina-project.org
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Vespolina\Tests\Brand\Gateway;

use Vespolina\Brand\Manager\BrandManager;
use Vespolina\Brand\Specification\BrandSpecification;
use Vespolina\Brand\Specification\TaxonomyNodeSpecification;
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
abstract class BrandGatewayTestCommon extends \PHPUnit_Framework_TestCase
{
    protected $brandGateway;

    public function testMatchBrandById()
    {
        $brands = $this->createAndPersistBrands();
        $targetBrand = $brands[0];

        $brand = $this->brandGateway->matchBrandById($targetBrand->getId());
        $this->assertNotNull($brand);

        $this->assertNull($this->brandGateway->matchBrandById(100000000));
    }

    protected function createAndPersistBrands($total = 10)
    {
        $brands = array();

        for ($i = 0; $i < $total; $i++) {
            $brand = $this->brandGateway->createBrand();
            $brand->setName('brand' . $i);
            $this->brandGateway->updateBrand($brand, false);
            $brands[] = $brand;
        }
        $this->brandGateway->flush();

        return $brands;
    }
}