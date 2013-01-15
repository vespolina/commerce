<?php

namespace Vespolina\Product\Gateway;

use Molino\MolinoInterface;
use Molino\SelectQueryInterface;
use Vespolina\Entity\Product\ProductInterface;
use Vespolina\Exception\InvalidInterfaceException;

class ProductGateway
{
    protected $molino;
    protected $productClass;

    /**
     * @param \Molino\MolinoInterface $molino
     * @param string $managedClass
     */
    public function __construct(MolinoInterface $molino, $productClass)
    {
        if (!class_exists($productClass) || !in_array('Vespolina\Entity\Product\ProductInterface', class_implements($productClass))) {
             throw new InvalidInterfaceException('Please have your product class implement Vespolina\Entity\Product\ProductInterface');
        }
        $this->molino = $molino;
        $this->productClass = $productClass;
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
            $queryClass = $this->partnerClass;
        }
        return $this->molino->$queryFunction($queryClass);
    }

    /**
     * @param \Vespolina\Entity\Product\ProductInterface $partner
     */
    public function deleteProduct(ProductInterface $partner)
    {
        $this->molino->delete($partner);
    }

    /**
     * @param \Molino\SelectQueryInterface $query
     * @return \Vespolina\Entity\Product\ProductInterface
     */
    public function findProduct(SelectQueryInterface $query)
    {
        return $query->one();
    }

    /**
     * @param \Molino\SelectQueryInterface $query
     * @return \Vespolina\Entity\Product\ProductInterface
     */
    public function findProducts(SelectQueryInterface $query)
    {
        return $query->all();
    }

    /**
     * @param \Vespolina\Entity\Product\ProductInterface $partner
     */
    public function persistProduct(ProductInterface $partner)
    {
        $this->molino->save($partner);
    }

    /**
     * @param \Vespolina\Entity\Product\ProductInterface $partner
     */
    public function updateProduct(ProductInterface $partner)
    {
        $this->molino->save($partner);
    }
}
