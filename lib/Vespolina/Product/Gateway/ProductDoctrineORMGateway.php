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
        return $this->em->createQueryBuilder($this->productClass)->from($this->productClass, 'p');
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

        $queryBuilder->select('p');


        if ($matchOne) {
            $query = $queryBuilder->getQuery()->setMaxResults(1);

            return $query->getSingleResult();
        } else {
            $query = $queryBuilder->getQuery();

            return $query->execute();
        }
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
