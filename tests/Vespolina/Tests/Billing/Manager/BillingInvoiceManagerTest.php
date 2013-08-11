<?php

use Vespolina\Entity\Billing\BillingRequest;
use Vespolina\Entity\Order\ItemInterface;
use Vespolina\Entity\Partner\PartnerInterface;
use Vespolina\Entity\Product\ProductInterface;
use Vespolina\Entity\Pricing\PricingContext;

/**
 * @group billing-invoice-manager
 */
class BillingInvoiceManagerTest //extends TestBaseManager
{
    const PRODUCT_ID = 'Paid Pro';

    public function testCreateInvoice()
    {
        $user = $this->createUser();
        $order = $this->createOrder($user->getPartner());

        $invoice = $this->getBillingInvoiceManager()->createInvoice($user->getPartner(), $order->getPricing());
        $this->assertNotNull($invoice->getId());
        $this->assertEquals($invoice->getAmountDue(), $order->getPricing()->get('totalValue'));
    }

    public function testTagAsCompleted()
    {
        $user = $this->createUser();
        $order = $this->createOrder($user->getPartner());

        $invoice = $this->getBillingInvoiceManager()->createInvoice($user->getPartner(), $order->getPricing());
        $this->getBillingInvoiceManager()->tagAsPaid($invoice);

        $this->assertNotNull($invoice->getInvoice());
        $this->assertEquals($invoice->getStatus(), BillingRequest::STATUS_PAID);
    }

    public function testEmailNotification()
    {
        $user = $this->createUser();
        $user
            ->setEmail('test@gmail.com')
            ->setUsername('test@gmail.com')
            ->setFirstName('testuser')
            ->setLastName('testuser')
            ->setPlainPassword(uniqid('test'))
        ;
        $this->getUserManager()->persistUser($user);

        $order = $this->createOrder($user->getPartner());

        $invoice = $this->getBillingInvoiceManager()->createInvoice($user->getPartner(), $order->getPricing());

        $this->getBillingInvoiceManager()->sendNotification($user, $invoice);

        $this->assertQueueContents();
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
    private function createOrder(PartnerInterface $partner)
    {
        /** @var ProductInterface $product  */
        $product = $this->getProductManager()->findProductBy(array('name' => self::PRODUCT_ID));
        $order = $this->getOrderManager()->createOrder();
        $order->setPartner($partner);
        /** @var ItemInterface $item  */
        $item = $this->getOrderManager()->addProductToOrder($order, $product);

        $context = new PricingContext();
        $context['partner'] = $order->getPartner();

        $this->getOrderManager()->updateOrderPricing($order, $context);
        $this->getOrderManager()->updateOrder($order);

        return $order;
    }
}
