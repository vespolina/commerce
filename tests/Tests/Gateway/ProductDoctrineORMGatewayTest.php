<?php
namespace Tests\Gateway;

use Vespolina\Product\Gateway\ProductDoctrineORMGateway;

class ProductDoctrineORMGatewayTest extends ProductGatewayTestCommon
{
    protected function setUp()
    {
        $molino = $this->getMock('Molino\MolinoInterface');
        $this->gateway = new ProductDoctrineORMGateway($molino, 'Vespolina\Entity\Product\Product');
    }
}
