<?php

namespace Vespolina\Product\Gateway;

use Molino\MolinoInterface;
use Molino\SelectQueryInterface;
use Pagerfanta\Pagerfanta;
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
            $queryClass = $this->productClass;
        }
        return $this->molino->$queryFunction($queryClass);
    }

    /**
     * @param \Vespolina\Entity\Product\ProductInterface $product
     */
    public function deleteProduct(ProductInterface $product)
    {
        $this->molino->delete($product);
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
    public function findProducts(SelectQueryInterface $query, $pager = false)
    {
        if (!$pager) {

            return $query->all();
        } else {

            return new Pagerfanta($query->createPagerfantaAdapter());
        }
    }

    /**
     * @param \Vespolina\Entity\Product\ProductInterface $product
     */
    public function persistProduct(ProductInterface $product)
    {
        $this->molino->save($product);
    }

    /**
     * @param \Vespolina\Entity\Product\ProductInterface $product
     */
    public function updateProduct(ProductInterface $product)
    {
        $this->molino->save($product);
    }
}
