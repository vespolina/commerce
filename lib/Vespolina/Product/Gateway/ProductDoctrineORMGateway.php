<?php

namespace Vespolina\Product\Gateway;

use Molino\MolinoInterface;
use Molino\SelectQueryInterface;
use Vespolina\Entity\Product\ProductInterface;
use Vespolina\Exception\InvalidInterfaceException;
use Vespolina\Product\Specification\SpecificationInterface;

class ProductDoctrineORMGateway extends ProductGateway
{
    protected $entityManager;

    public function __construct($entityManager, $productClass)
    {
        $this->em = $entityManager;
        parent::__construct($productClass, 'DoctrineORM');
    }

    public function createQuery()
    {
        return $this->em->createQueryBuilder($this->productClass);
    }

    /**
     * @param \Vespolina\Entity\Product\ProductInterface $product
     */
    public function deleteProduct(ProductInterface $product, $andFlush = true)
    {
        $this->em->remove($product);
        if ($andFlush) $this->flush();
    }

    public function flush()
    {
        $this->em->flush();
    }

    protected function executeSpecification(SpecificationInterface $specification, $matchOne = false)
    {
        $queryBuilder = $this->createQuery();
        $this->getSpecificationWalker()->walk($specification, $queryBuilder);
        $query = $queryBuilder->getQuery();

        if ($matchOne) {

            return $query->getSingleResult();
        } else {

            return $query->execute();
        }
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


    public function matchProduct(SpecificationInterface $specification)
    {
        return $this->executeSpecification($specification, true);
    }

    public function matchProducts(SpecificationInterface $specification)
    {
        return $this->executeSpecification($specification);
    }


    /**
     * @param \Vespolina\Entity\Product\ProductInterface $product
     */
    public function persistProduct(ProductInterface $product, $andFlush = true)
    {
        $this->em->persist($product);
        if ($andFlush) $this->flush();
    }

    /**
     * @param \Vespolina\Entity\Product\ProductInterface $product
     */
    public function updateProduct(ProductInterface $product, $andFlush = true)
    {
        $this->em->persist($product);
        if ($andFlush) $this->flush();
    }
}
