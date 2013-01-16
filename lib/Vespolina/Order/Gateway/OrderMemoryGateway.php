<?php
/**
 * (c) 2012 Vespolina Project http://www.vespolina-project.org
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Vespolina\Order\Gateway;

use Gateway\Query;
use Vespolina\Order\Gateway\OrderGatewayInterface;
use Vespolina\Entity\Order\OrderInterface;

class OrderMemoryGateway implements OrderGatewayInterface
{
    protected $carts;
    protected $deletedOrders;
    protected $lastOrder;
    protected $ids = array();

    public function deleteOrder(OrderInterface $cart)
    {
        $cartId = $cart->getId();
        unset($this->carts);
        $this->deletedOrders[$cartId] = $cart;
    }

    public function getLastOrder()
    {
        return $this->lastOrder;
    }

    public function findOrders(Query $query)
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

    public function persistOrder(OrderInterface $cart)
    {
        $cartId = $this->generateNewId();
        $rp = new \ReflectionProperty($cart, 'id');
        $rp->setAccessible(true);
        $rp->setValue($cart, $cartId);
        $rp->setAccessible(false);
        $this->carts[$cartId] = $cart;
        $this->lastOrder = $cart;
    }

    public function updateOrder(OrderInterface $cart)
    {
        $cartId = $cart->getId();
        if (!isset($this->carts[$cartId])) {
            throw new \Exception('This cart has not been persisted');
        }
        $this->carts[$cartId] = $cart;
        $this->lastOrder = $cart;
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
