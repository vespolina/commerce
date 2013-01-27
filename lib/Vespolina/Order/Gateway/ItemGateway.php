<?php

namespace Vespolina\Order\Gateway;

use Molino\MolinoInterface;
use Molino\SelectQueryInterface;
use InvalidArgumentException;
use Vespolina\Entity\Order\ItemInterface;
use Vespolina\Exception\InvalidInterfaceException;

class ItemGateway implements ItemGatewayInterface
{
    protected $molino;
    protected $itemClass;

    /**
     * @param \Molino\MolinoInterface $molino
     * @param string $itemClass
     * @throws \Vespolina\Exception\InvalidInterfaceException
     */
    public function __construct(MolinoInterface $molino, $itemClass)
    {
        if (!class_exists($itemClass) || !in_array('Vespolina\Entity\Order\ItemInterface', class_implements($itemClass))) {
            throw new InvalidInterfaceException('Please have your order class implement Vespolina\Entity\Order\ItemInterface');
        }
        $this->molino = $molino;
        $this->orderClass = $itemClass;
    }

    /**
     * @param \Molino\SelectQueryInterface $query
     * @return array
     */
    public function findItems(SelectQueryInterface $query)
    {
        return $query->all();
    }

    /**
     * @param \Molino\SelectQueryInterface $query
     * @return type
     */
    public function findItem(SelectQueryInterface $query)
    {
        return $query->one();
    }

    /**
     * @param \Vespolina\Entity\Item\ItemInterface $item
     */
    public function persistItem(ItemInterface $item)
    {
        $this->molino->save($item);
    }

    /**
     * @param \Vespolina\Entity\Item\ItemInterface $item
     */
    public function updateItem(ItemInterface $item)
    {
        $this->molino->save($item);
    }

    /**
     * @param \Vespolina\Entity\Item\ItemInterface $item
     */
    public function deleteItem(ItemInterface $item)
    {
        $this->molino->delete($item);
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
