<?php

namespace Vespolina\Tests;

use Molino\Memory\Molino;
use Vespolina\EventDispatcher\NullDispatcher;
use Vespolina\Order\Gateway\OrderGateway;
use Vespolina\Order\Manager\OrderManager;

/**
 * Class TestsCommon
 * @package Vespolina\Tests
 */
class TestsCommon
{
    public function getOrderManager($classes = null, $dispatcher = null)
    {
        if (!$classes) {
            $classes = array(
                'cart' => 'Vespolina\Entity\Order\Cart',
                'events' => 'Vespolina\Entity\Order\CartEvents',
                'item' => 'Vespolina\Entity\Order\Item',
                'order' => 'Vespolina\Entity\Order\Order'
            );
        }

        return new OrderManager($this->getOrderGateway(), $classes, $dispatcher);
    }

    public function getOrderGateway()
    {
        return new OrderGateway(new Molino(), 'Vespolina\Entity\Order\Order');
    }
}