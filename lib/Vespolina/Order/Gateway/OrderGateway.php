<?php

namespace Vespolina\Order\Gateway;

use Molino\SelectQueryInterface;
use InvalidArgumentException;
use Vespolina\Entity\Order\OrderInterface;

class OrderGateway implements OrderGatewayInterface
{
    protected $orderClass = 'Vespolina\Entity\Order\Order'; // used in base class

    /** @var \Molino\MolinoInterface */
    protected $molino;

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
     * @return type
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
