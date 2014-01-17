<?php

namespace Vespolina\Tests\Order;

use Molino\Memory\Molino;
use Vespolina\Entity\Product\Product;
use Vespolina\EventDispatcher\NullDispatcher;
use Vespolina\Order\Gateway\OrderGateway;
use Vespolina\Order\Manager\OrderManager;
use Vespolina\Tests\Pricing\PricingTestsCommon;
use Vespolina\Tests\Product\ProductTestsCommon;

/**
 * Class TestsCommon
 * @package Vespolina\Tests
 */
class OrderTestsCommon
{
    public static function createSimpleOrder($product = null)
    {
        $manager = self::getOrderManager();
        $order = $manager->createOrder();
        if (!$product) {
            $product = ProductTestsCommon::createProduct();
        }
        $manager->addProductToOrder($order, $product);

        return $order;
    }

    /**
     * @param null $classes
     * @param null $dispatcher
     *
     * @return OrderManager
     */
    public static function getOrderManager($classes = null, $managerMapping = null, $dispatcher = null)
    {
        if (!$classes) {
            $classes = array(
                'eventsClass' => 'Vespolina\Entity\Order\OrderEvents',
                'itemClass' => 'Vespolina\Entity\Order\Item',
                'orderClass' => 'Vespolina\Entity\Order\Order',
            );
        }
        if (!$managerMapping) {
            $managerMapping = array(
                'pricingManager' => PricingTestsCommon::getPricingManager(),
            );
        }

        return new OrderManager(self::getOrderGateway(), $classes, $managerMapping, $dispatcher);
    }

    /**
     * @param null $molino
     * @param string $orderClass
     *
     * @return OrderGateway
     */
    public static function getOrderGateway($molino = null, $orderClass = 'Vespolina\Entity\Order\Order')
    {
        if (!$molino) {
            $molino = new Molino();
        }

        return new OrderGateway($molino, $orderClass);
    }
}