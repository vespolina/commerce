<?php

/**
 * (c) 2011 - ∞ Vespolina Project http://www.vespolina-project.org
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Vespolina\Tests\Pricing\Manager;

use Vespolina\Pricing\Manager\PricingManager;
use Vespolina\Tests\Pricing\PricingTestsCommon;

/**
 * @author Daniel Kucharski <daniel@xerias.be>
 */
class PricingManagerTest extends \PHPUnit_Framework_TestCase
{
    public function testCreatePricingManager()
    {
        $pricingManager = new PricingManager('EUR');
        $this->assertNotNull($pricingManager);
    }

    public function testCreateDefaultPricingSet()
    {
        $pricingSet = PricingTestsCommon::getPricingManager()->createPricing();
        $this->assertNotNull($pricingSet);
    }

    public function testAddConfiguration()
    {
        $pricingManager = PricingTestsCommon::getPricingManager();
        $pricingManager->addConfiguration('test1',
                                          'Vespolina\Entity\Pricing\PricingSet');
        $pricingSet = $pricingManager->createPricing(null, 'test1');
        $this->assertNotNull($pricingSet);
    }
}