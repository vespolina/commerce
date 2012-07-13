<?php
/**
 * (c) 2012 Vespolina Project http://www.vespolina-project.org
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Vespolina\CartBundle\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use Vespolina\Entity\CartInterface;
use Vespolina\Entity\Item;
use Vespolina\Entity\OrderInterface;

/**
 * CartItem implements a basic cart item implementation
 *
 * @author Daniel Kucharski <daniel@xerias.be>
 * @author Richard Shank <develop@zestic.com>
 */
abstract class CartItem extends Item
{
    protected $description;
    protected $isRecurring;
    protected $name;
    protected $options;
    protected $paymentInstruction;
    protected $productId;
    protected $pricingSet;
    protected $quantity;
    protected $state;
    protected $totalPrice;

    public function __construct($product = null)
    {
        $this->product = $product;
        $this->isRecurring = false;
        $this->options = array();
        $this->prices = array();
        $this->quantity = 1;
    }

    public function getId()
    {
        return $this->id;
    }

    /**
     * @inheritdoc
     */
    public function getCart()
    {
        return $this->getParent();
    }


    public function getPricingSet()
    {
        return $this->pricingSet;
    }

    /**
     * @inheritdoc
     */
    public function setCart(CartInterface $cart)
    {
        $this->setParent($cart);
    }

    /**
     * @inheritdoc
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @inheritdoc
     */
    public function getDescription()
    {
        return $this->description;
    }

    public function setPaymentInstruction($paymentInstruction)
    {
        $this->paymentInstruction = $paymentInstruction;
    }

    public function getPaymentInstruction()
    {
        return $this->paymentInstruction;
    }

    public function setPricingSet($pricingSet)
    {
        $this->pricingSet = $pricingSet;
    }

    /**
     * @inheritdoc
     */
    public function isRecurring()
    {
        return $this->isRecurring;
    }

    public function setTotalPrice($totalPrice)
    {
        $this->totalPrice = $totalPrice;
    }

    public function getTotalPrice()
    {
        return $this->totalPrice;
    }
}
