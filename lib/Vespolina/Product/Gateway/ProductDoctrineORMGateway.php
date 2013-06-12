<?php

namespace Vespolina\Product\Gateway;

use Molino\MolinoInterface;
use Molino\SelectQueryInterface;
use Vespolina\Entity\Product\ProductInterface;
use Vespolina\Exception\InvalidInterfaceException;
use Vespolina\Product\Specification\SpecificationInterface;

class ProductDoctrineORMGateway extends ProductGateway
{
    protected $molino;

    /**
     * @param \Molino\MolinoInterface $molino
     * @param string $managedClass
     */
    public function __construct(MolinoInterface $molino, $productClass)
    {
        $this->molino = $molino;
        parent::__construct($productClass, 'DoctrineORM');
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
            $queryClass = $this->productClass;
        }
        return $this->molino->$queryFunction($queryClass);
    }

    /**
     * @param \Vespolina\Entity\Product\ProductInterface $product
     */
    public function deleteProduct(ProductInterface $product, $andFlush = true)
    {
        $this->molino->delete($product);
    }

    public function flush()
    {

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


    public function matchProducts(SpecificationInterface $specification)
    {

    }

    /**
     * @param \Vespolina\Entity\Product\ProductInterface $product
     */
    public function persistProduct(ProductInterface $product, $andFlush = true)
    {
        $this->molino->save($product);
    }

    /**
     * @param \Vespolina\Entity\Product\ProductInterface $product
     */
    public function updateProduct(ProductInterface $product, $andFlush = true)
    {
        $this->molino->save($product);
    }
}
