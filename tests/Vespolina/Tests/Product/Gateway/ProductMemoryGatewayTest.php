<?php

/**
 * (c) 2011 - ∞ Vespolina Project http://www.vespolina-project.org
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Vespolina\Tests\Product\Gateway;

use Vespolina\Product\Gateway\ProductMemoryGateway;
use Vespolina\Taxonomy\Gateway\TaxonomyMemoryGateway;


class ProductMemoryGatewayTest extends ProductGatewayTestCommon
{
    protected function setUp()
    {
        $this->productGateway = new ProductMemoryGateway('Vespolina\Entity\Product\Product');
        $this->taxonomyGateway = new TaxonomyMemoryGateway('Vespolina\Entity\Taxonomy\TaxonomyNode');

        parent::setUp();
    }
}
