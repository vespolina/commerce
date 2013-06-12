<?php

use Vespolina\Product\Gateway\ProductDoctrineORMGateway;

class ProductDoctrineORMGatewayTest extends \PHPUnit_Framework_TestCase
{
    public function testConstruct()
    {
        $molino = $this->getMock('Molino\MolinoInterface');
        $gateway = new ProductDoctrineORMGateway($molino, 'Vespolina\Entity\Product\Product');
        $this->assertInstanceOf('Vespolina\Product\Gateway\ProductGateway', $gateway);

        $this->setExpectedException(
            'Vespolina\Exception\InvalidInterfaceException',
            'Please have your product class implement Vespolina\Entity\Product\ProductInterface'
        );
        $gateway = new ProductDoctrineORMGateway($molino, 'InvalidClass');
        $this->setExpectedException(
            'Vespolina\Exception\InvalidInterfaceException',
            'Please have your product class implement Vespolina\Entity\Product\ProductInterface'
        );
        $gateway = new ProductDoctrineORMGateway($molino, 'Vespolina\Entity\Product\Product');
    }
}
