<?php

namespace Vespolina\Order\Gateway;

use Molino\MolinoInterface;
use Molino\SelectQueryInterface;
use InvalidArgumentException;
use Vespolina\Entity\Order\OrderInterface;
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
            throw new InvalidInterfaceException('Please have your order class implement Vespolina\Entity\Product\ProductInterface');
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
        return $query->one();
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
        $this->molino->refresh($order);
        $this->molino->save($order);
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

        return $this->molino->{$queryFunction}($queryClass);
    }
}
