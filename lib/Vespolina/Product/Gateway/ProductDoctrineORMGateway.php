<?php

/**
 * (c) 2011 - ∞ Vespolina Project http://www.vespolina-project.org
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Vespolina\Product\Gateway;

use Doctrine\ORM\EntityManager;
use Vespolina\Entity\Product\ProductInterface;
use Vespolina\Exception\InvalidInterfaceException;
use Vespolina\Specification\SpecificationInterface;

/**
 * Defines a gateway to ORM (eg. MySQL, Postgress, Sqlite) using Doctrine ORM
 *
 * @author Daniel Kucharski <daniel@xerias.be>
 * @author Richard Shank <develop@zestic.com>
 */
class ProductDoctrineORMGateway extends ProductGateway
{
    protected $entityManager;

    public function __construct(EntityManager $entityManager, $productClass)
    {
        $this->em = $entityManager;
        parent::__construct($productClass, 'DoctrineORM');
    }

    /**
     * @inheritdoc
     */
    public function createQuery()
    {
        return $this->em->createQueryBuilder($this->productClass)->from($this->productClass, 'p');
    }

    /**
     * @inheritdoc
     */
    public function deleteProduct(ProductInterface $product, $andFlush = true)
    {
        $this->em->remove($product);
        if ($andFlush) {
            $this->flush();
        }
    }

    /**
     * @inheritdoc
     */
    public function flush()
    {
        $this->em->flush();
    }

    /**
     * @inheritdoc
     */
    public function persistProduct(ProductInterface $product, $andFlush = true)
    {
        $this->em->persist($product);
        if ($andFlush) {
            $this->flush();
        }
    }

    /**
     * @inheritdoc
     */
    public function updateProduct(ProductInterface $product, $andFlush = true)
    {
        $this->em->persist($product);
        if ($andFlush) {
            $this->flush();
        }
    }

    /**
     * Execute the requested specification and return one or multiple products (as a collection)
     *
     * @param SpecificationInterface $specification
     * @param bool $matchOne
     * @return mixed
     */
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
}
