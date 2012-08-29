<?php
/**
 * (c) 2012 Vespolina Project http://www.vespolina-project.org
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Vespolina\Cart\Gateway;

use Gateway\Query;
use Vespolina\Cart\Gateway\CartGatewayInterface;
use Vespolina\Entity\Order\CartInterface;

class CartMemoryGateway implements CartGatewayInterface
{
    protected $carts;
    protected $deletedCarts;
    protected $lastCart;
    protected $ids = array();

    public function deleteCart(CartInterface $cart)
    {
        $cartId = $cart->getId();
        unset($this->carts);
        $this->deletedCarts[$cartId] = $cart;
    }

    public function getLastCart()
    {
        return $this->lastCart;
    }

    public function findCarts(Query $query)
    {
        $criteria = $query->getCriteria();

        $results = array();
        if (isset($criteria['id'])) {
            $value = $criteria['id'][1];
            switch ($criteria['id'][0]) {
                case 'equals':
                    if (isset($this->carts[$value])) {
                        $results[$value] = $this->carts[$value];
                    }
                    break;
            }
        }

        return (sizeof($results) === 1) ? array_pop($results) : $results;
    }

    public function persistCart(CartInterface $cart)
    {
        $cartId = $this->generateNewId();
        $rp = new \ReflectionProperty($cart, 'id');
        $rp->setAccessible(true);
        $rp->setValue($cart, $cartId);
        $rp->setAccessible(false);
        $this->carts[$cartId] = $cart;
        $this->lastCart = $cart;
    }

    public function updateCart(CartInterface $cart)
    {
        $cartId = $cart->getId();
        if (!isset($this->carts[$cartId])) {
            throw new \Exception('This cart has not been persisted');
        }
        $this->carts[$cartId] = $cart;
        $this->lastCart = $cart;
    }

    protected function generateNewId()
    {
        do {
            $id = rand(0, 10000);
        } while (in_array($id, $this->ids));
        $this->ids[] = $id;

        return $id;
    }
}
