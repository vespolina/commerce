<?php

namespace Vespolina\Tests\Pricing;

use Vespolina\Pricing\Manager\PricingManager;

/**
 * Class PricingTestsCommon
 * @package Vespolina\Tests
 */
class PricingTestsCommon
{
    /**
     * @param string | null $defaultCurrency
     * @param array | null $configuration
     *
     * @return PricingManager
     */
    public static function getPricingManager($defaultCurrency = null, array $configuration = array())
    {
        return new PricingManager($defaultCurrency, $configuration);
    }
}