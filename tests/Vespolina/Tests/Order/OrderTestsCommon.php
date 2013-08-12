<?php

namespace Vespolina\Tests\Order;

use Molino\Memory\Molino;
use Vespolina\Entity\Product\Product;
use Vespolina\EventDispatcher\NullDispatcher;
use Vespolina\Order\Gateway\OrderGateway;
use Vespolina\Order\Manager\OrderManager;

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
        $product = ProductTestsCommon::createProduct();
        $manager->addProductToOrder($order, $product);

        return $order;
    }

    /**
     * @param null $classes
     * @param null $dispatcher
     *
     * @return OrderManager
     */
    public static function getOrderManager($classes = null, $dispatcher = null)
    {
        if (!$classes) {
            $classes = array(
                'cart' => 'Vespolina\Entity\Order\Cart',
                'events' => 'Vespolina\Entity\Order\CartEvents',
                'item' => 'Vespolina\Entity\Order\Item',
                'order' => 'Vespolina\Entity\Order\Order'
            );
        }

        return new OrderManager(self::getOrderGateway(), $classes, $dispatcher);
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