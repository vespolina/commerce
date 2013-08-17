<?php

/**
 * (c) 2011 - ∞ Vespolina Project http://www.vespolina-project.org
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

use Vespolina\Partner\Gateway\PartnerGateway;

class PartnerGatewayTest extends \PHPUnit_Framework_TestCase
{
    public function testConstruct()
    {
        $molino = $this->getMock('Molino\MolinoInterface');
        $gateway = new PartnerGateway($molino, 'Vespolina\Entity\Partner\Partner');
        $this->assertInstanceOf('Vespolina\Partner\Gateway\PartnerGateway', $gateway);

        $this->setExpectedException(
            'Vespolina\Exception\InvalidInterfaceException',
            'Please have your partner class implement Vespolina\Entity\Partner\PartnerInterface'
        );
        $gateway = new PartnerGateway($molino, 'InvalidClass');
        $this->setExpectedException(
            'Vespolina\Exception\InvalidInterfaceException',
            'Please have your partner class implement Vespolina\Entity\Partner\PartnerInterface'
        );
        $gateway = new PartnerGateway($molino, 'Vespolina\Entity\Product\Product');
    }
}
