<?php
/**
 * (c) Vespolina Project http://www.vespolina-project.org
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Vespolina\Order\Util;

use Vespolina\Entity\ItemInterface;
use Vespolina\Entity\ProductInterface;
use Vespolina\Entity\Order\OrderInterface;
use Vespolina\Order\Manager\OrderManagerInterface;

/**
 * Handles advanced  order manipulations such as:
 *  - create an order from a cart
 *
 * @author Daniel Kucharski <daniel@xerias.be>
 */
class OrderManipulator
{
    protected $orderManager;

    public function __construct(OrderManagerInterface $orderManager) {

        $this->orderManager = $orderManager;
    }

    public function createOrderFromCart(OrderInterface $cart) {

        $order = $this->orderManager->createOrder();
        $order->setOwner($cart->getOwner());

        //$order->setOrderDate(new \DateTime());
        $this->orderManager->setOrderState($order, 'unprocessed');
        $order->setPricing($cart->getPricing());    //TODO: order pricing should be independent from a cart

        //$fulfillmentAgreement = $this->orderManager->createFulfillmentAgreement();
        //$paymentAgreement = $this->orderManager->createPaymentAgreement();

        //$order->setFulfillmentAgreement($fulfillmentAgreement);
        //$order->setPaymentAgreement($paymentAgreement);

        $items = $cart->getItems();

        if (null != $items) {

            foreach($cart->getItems() as $cartItem) {

                $orderItem = $this->orderManager->addProductToOrder($order, $cartItem->getProduct());
                //$orderItem->setPricing($cartItem->getPricing());
                $this->orderManager->setItemQuantity($orderItem, $cartItem->getQuantity());
                $this->orderManager->setOrderItemState($orderItem, 'unprocessed');
            }
        }

        return $order;
    }
}