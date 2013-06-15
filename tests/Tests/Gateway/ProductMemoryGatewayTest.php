<?php
namespace Tests\Gateway;

use Vespolina\Product\Gateway\ProductMemoryGateway;
use Vespolina\Product\Specification\ProductSpecification;

class ProductMemoryGatewayTest extends ProductGatewayTestCommon
{
    protected function setUp()
    {
        $this->gateway = new ProductMemoryGateway('Vespolina\Entity\Product\Product');
        parent::setUp();
    }
}
