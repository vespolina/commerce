<?php

/**
 * (c) 2011 - ∞ Vespolina Project http://www.vespolina-project.org
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

use Vespolina\Pricing\Manager\PricingManager;

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
        $pricingSet = $this->getPricingManager()->createPricingSet();
        $this->assertNotNull($pricingSet);
    }

    public function testAddConfiguration()
    {
        $pricingManager = $this->getPricingManager();
        $pricingManager->addConfiguration('test1',
                                          'Vespolina\Entity\Pricing\PricingSet');
        $pricingSet = $pricingManager->createPricingSet('test1');
        $this->assertNotNull($pricingSet);
    }

    protected function getPricingManager()
    {
        return new PricingManager();
    }

}