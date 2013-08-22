<?php

namespace Vespolina\Tests\Product;

use Vespolina\Entity\Pricing\PricingSetInterface;
use Vespolina\Entity\Product\Product;
use Vespolina\EventDispatcher\NullDispatcher;
use Vespolina\Product\Gateway\ProductGateway;
use Vespolina\Product\Gateway\ProductMemoryGateway;
use Vespolina\Product\Manager\ProductManager;

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
            $pricing = PriceTestCommon::getPricingManager()->createPricing(5);
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
    public static function getProductManager($classes = null, $configuration = null)
    {
        if (!$classes) {
            $classes = array(
                'attribute' => 'Vespolina\Entity\Product\Attribute',
                'merchandise' => 'Vespolina\Entity\Product\Merchandise',
                'option' => 'Vespolina\Entity\Product\Option',
                'product' => 'Vespolina\Entity\Product\Product'
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