<?php

use Vespolina\Partner\Gateway\PartnerGateway;

class PartnerGatewayTest extends \PHPUnit_Framework_TestCase
{
    public function testConstruct()
    {
        $gateway = new PartnerGateway($molino, 'Vespolina\Entity\Partner\Partner');
        $this->assertInstanceOf('Vespolina\Partner\Gateway\PartnerGateway', $gateway);
        
        $this->setExpectedException('Exception', '');
        $gateway = new PartnerGateway($molino, 'InvalidClass');
    }
}
