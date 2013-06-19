<?php
namespace Tests\Gateway;

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
