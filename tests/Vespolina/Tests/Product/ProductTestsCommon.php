<?php

namespace Vespolina\Tests\Product;

use Vespolina\Entity\Pricing\PricingSetInterface;
use Vespolina\Entity\Product\Product;
use Vespolina\EventDispatcher\NullDispatcher;
use Vespolina\Product\Gateway\ProductGateway;
use Vespolina\Product\Gateway\ProductMemoryGateway;
use Vespolina\Product\Manager\ProductManager;
use Vespolina\Tests\Pricing\PricingTestsCommon;

/**
 * Class ProductTestsCommon
 * @package Vespolina\Tests
 */
class ProductTestsCommon
{
    public static function createProduct(PricingSetInterface $pricing = null)
    {
        $manager = self::getProductManager();
        $product = $manager->createProduct();
        if (!$pricing) {
            $pricing = PricingTestsCommon::getPricingManager()->createPricing(5);
        }
        $product->setPricing($pricing);

        return $product;
    }

    /**
     * @param null $classes
     * @param null $dispatcher
     *
     * @return ProductManager
     */
    public static function getProductManager($classes = null, $configuration = array())
    {
        if (!$classes) {
            $classes = array(
                'attributeClass' => 'Vespolina\Entity\Product\Attribute',
                'merchandiseClass' => 'Vespolina\Entity\Product\Merchandise',
                'optionClass' => 'Vespolina\Entity\Product\Option',
                'productClass' => 'Vespolina\Entity\Product\Product'
            );
        }

        return new ProductManager(self::getProductGateway(), $classes, $configuration);
    }

    /**
     * @param string $productClass
     *
     * @return ProductGateway
     */
    public static function getProductGateway($productClass = 'Vespolina\Entity\Product\Product')
    {
        return new ProductMemoryGateway($productClass);
    }
}