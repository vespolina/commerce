<?php

use Vespolina\Product\Gateway\ProductGateway;

class ProductGatewayTest extends \PHPUnit_Framework_TestCase
{
    public function testConstruct()
    {
        $molino = $this->getMock('Molino\MolinoInterface');
        $gateway = new ProductGateway($molino, 'Vespolina\Entity\Product\Product');
        $this->assertInstanceOf('Vespolina\Product\Gateway\ProductGateway', $gateway);

        $this->setExpectedException(
            'Vespolina\Exception\InvalidInterfaceException',
            'Please have your product class implement Vespolina\Entity\Product\ProductInterface'
        );
        $gateway = new ProductGateway($molino, 'InvalidClass');
        $this->setExpectedException(
            'Vespolina\Exception\InvalidInterfaceException',
            'Please have your product class implement Vespolina\Entity\Product\ProductInterface'
        );
        $gateway = new ProductGateway($molino, 'Vespolina\Entity\Product\Product');
    }
}
