<?php

use ImmersiveLabs\CaraCore\Tests\TestBaseManager;
use Vespolina\Entity\Partner\PartnerInterface;
use Vespolina\Entity\Order\ItemInterface;
use Vespolina\Entity\Product\ProductInterface;
/**
 * @group billing-invoice-manager
 */
class BillingInvoiceManagerTest extends TestBaseManager
{
    const PRODUCT_ID = 'Paid Pro';

    public function testCreateInvoice()
    {
        $user = $this->createUser();
        $order = $this->createOrder();
    }

    /**
     * @return ImmersiveLabs\CaraCore\Entity\User
     */
    private function createUser()
    {
        $user = $this->getUserManager()->createUser();
        return $user;
    }

    /**
     * @return Vespolina\Entity\Order\OrderInterface
     */
    private function createOrder()
    {
        /** @var ProductInterface $product  */
        $product = $this->getProductManager()->findProductBy(array('name' => self::PRODUCT_ID));
        $order = $this->getOrderManager()->createOrder();
        /** @var ItemInterface $item  */
        $item = $this->getOrderManager()->addProductToOrder($order, $product);
        $item->setPricing($product->getPricing());
        $order->setPricing($product->getPricing());
//        $order->setTotalPrice($product->getPricing()->getNetValue());
        $this->getOrderManager()->updateOrder($order);

        return $order;
    }
}
