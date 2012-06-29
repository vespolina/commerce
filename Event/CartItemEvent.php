<?php
/**
 * (c) 2012 Vespolina Project http://www.vespolina-project.org
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Vespolina\CartBundle\Event;

use Symfony\Component\HttpKernel\Event\KernelEvent;
use Vespolina\Entity\ItemInterface;
use Vespolina\Entity\OrderInterface;

class CartItemEvent extends KernelEvent
{
    /**
     * @var \Vespolina\Entity\ItemInterface $cartItem
     */
    protected $cartItem;

    public function __construct(ItemInterface $cartItem)
    {
        $this->cartItem = $cartItem;
    }

    public function getCartItem()
    {
        return $this->cartItem;
    }
}