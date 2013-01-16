<?php
/**
 * (c) 2012 Vespolina Project http://www.vespolina-project.org
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Vespolina\Order\Gateway;

use Gateway\Query;
use ImmersiveLabs\CaraCore\Exception\InvalidArgumentException;
use Molino\MolinoInterface;
use Vespolina\Exception\InvalidInterfaceException;
use Vespolina\Order\Gateway\OrderGatewayInterface;
use Vespolina\Entity\Order\OrderInterface;

class OrderMemoryGateway implements OrderGatewayInterface
{
    protected $molino;
    protected $orderClass;

    protected $carts;
    protected $deletedOrders;
    protected $lastOrder;
    protected $ids = array();

    /**
     * @param \Molino\MolinoInterface $molino
     * @param $orderClass
     * @throws \Vespolina\Exception\InvalidInterfaceException
     */
    public function __construct(MolinoInterface $molino, $orderClass)
    {
        if (!class_exists($orderClass) || !in_array('Vespolina\Entity\Order\OrderInterface', class_implements($orderClass))) {
            throw new InvalidInterfaceException('Please have your order class implement Vespolina\Entity\Order\OrderInterface');
        }
        $this->molino = $molino;
        $this->orderClass = $orderClass;
    }

    public function createQuery($type, $queryClass = null)
    {
        $type = ucfirst(strtolower($type));
        if (!in_array($type, array('Delete', 'Select', 'Update'))) {
            throw new InvalidArgumentException($type . ' is not a valid Query type');
        }
        $queryFunction = 'create' . $type . 'Query';

        if (!$queryClass) {
            $queryClass = $this->orderClass;
        }

        return $this->molino->$queryFunction($queryClass);
    }

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
