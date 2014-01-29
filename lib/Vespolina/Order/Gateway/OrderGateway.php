<?php

/**
 * (c) 2011 - ∞ Vespolina Project http://www.vespolina-project.org
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Vespolina\Order\Gateway;

use Molino\MolinoInterface;
use Molino\SelectQueryInterface;
use InvalidArgumentException;
use Vespolina\Entity\Order\OrderInterface;
use Vespolina\Entity\Order\ItemInterface;
use Vespolina\Exception\InvalidInterfaceException;

class OrderGateway implements OrderGatewayInterface
{
    protected $molino;
    protected $orderClass;

    /**
     * @param \Molino\MolinoInterface $molino
     * @param string $orderClass
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

    /**
     * @param \Molino\SelectQueryInterface $query
     * @return array
     */
    public function findOrders(SelectQueryInterface $query)
    {
        return $query->all();
    }

    /**
     * @param \Molino\SelectQueryInterface $query
     * @return type
     */
    public function findOrder(SelectQueryInterface $query)
    {
        $order = $query->one();
        /*
        $items = [];
        foreach ($order->getItems() as $item) {
            $items[] = $item;
        }
        $order->setItems($items);
*/
        return $order;
    }

    /**
     * @param \Vespolina\Entity\Order\OrderInterface $order
     */
    public function persistOrder(OrderInterface $order)
    {
        $this->molino->save($order);
    }

    /**
     * @param \Vespolina\Entity\Order\OrderInterface $order
     */
    public function updateOrder(OrderInterface $order)
    {
        $this->molino->save($order);
    }

    /**
     * Update an Order item that has been persisted.  The Order itemwill be immediately flushed in the database
     *
     * @param \Vespolina\Entity\Order\OrderInterface $cart
     */
    public function updateOrderItem(ItemInterface $orderItem)
    {
        $this->molino->save($orderItem->getParent());
    }

    /**
     * @param \Vespolina\Entity\Order\OrderInterface $order
     */
    public function deleteOrder(OrderInterface $order)
    {
        $this->molino->delete($order);
    }

    /**
     * @param string $type
     * @param type $queryClass
     * @return \Molino\Doctrine\ORM\BaseQuery
     * @throws InvalidArgumentException
     */
    public function createQuery($queryType, $queryClass = null)
    {
        $queryType = ucfirst(strtolower($queryType));
        if (!in_array($queryType, array('Delete', 'Select', 'Update'))) {
            throw new InvalidArgumentException($queryType . ' is not a valid Query type');
        }
        $queryFunction = 'create' . $queryType . 'Query';

        if (!$queryClass) {
            $queryClass = $this->orderClass;
        }

        return $this->molino->{$queryFunction}($queryClass);
    }
}
