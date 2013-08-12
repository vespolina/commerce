<?php

/**
 * (c) 2011 - ∞ Vespolina Project http://www.vespolina-project.org
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Vespolina\Pricing\Manager;

use Vespolina\Entity\Pricing\PricingSetInterface;

/**
 * The pricing manager is responsible for instantiating pricing sets,
 * registering pricing set definitions and so on...
 *
 * @author Daniel Kucharski <daniel@xerias.be>
 */
interface PricingManagerInterface
{
    /**
     * Create a pricing set of type $type
     * Optionally pass pricing values to the newly created pricing set. For a simple Pricing, an integer, float or MoneyI
     *
     * @param mixed $pricingValues
     * @param string $type
     *
     * @return \Vespolina\Entity\Pricing\PricingSetInterface
     * @throws \Exception
     */
    function createPricing($pricingValues = null, $type = 'default');

    /**
     * Add a pricing set configuration
     *
     * @param $name
     * @param $pricingSetClass
     * @param array $pricingElementClasses
     * @return mixed
     */
    function addConfiguration($name, $pricingSetClass, array $pricingElementClasses = array());

}
