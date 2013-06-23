<?php

namespace Vespolina\Product\Gateway;

use Vespolina\Entity\Product\ProductInterface;
use Vespolina\Exception\InvalidInterfaceException;
use Vespolina\Product\Specification\SpecificationInterface;

class ProductDoctrineMongoDBGateway extends ProductGateway
{

    protected $dm;
    /**
     * @param string $managedClass
     */
    public function __construct($documentManager, $productClass)
    {
        $this->dm = $documentManager;
        parent::__construct($productClass, 'DoctrineMongoDB');
    }

    public function createQuery()
    {
        return $this->dm->createQueryBuilder($this->productClass);
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

    public function matchProduct(SpecificationInterface $specification)
    {
        return $this->executeSpecification($specification, true);
    }


    public function matchProducts(SpecificationInterface $specification)
    {
        return $this->executeSpecification($specification, false);
    }

    /**
     * Delete a Product that has been persisted and optionally flush that link.
     * Systems that allow for a delayed flush can use the $andFlush parameter, other
     * systems would disregard the flag. The success of the process is returned.
     *
     * @param \Vespolina\Entity\ProductInterface $product
     *
     * @param boolean $andFlush
     */
    function deleteProduct(ProductInterface $product, $andFlush = false)
    {
        $this->dm->remove($product);
        if ($andFlush) $this->flush();
    }

    /**
     * Flush any changes to the database
     */
    function flush()
    {
        $this->dm->flush();
    }

    /**
     * Persist a Product that has been created and optionally flush that link.
     * Systems that allow for a delayed flush can use the $andFlush parameter, other
     * systems would disregard the flag. The success of the process is returned.
     *
     * @param Vespolina\Entity\ProductInterface $product
     *
     * @param boolean $andFlush
     */
    function persistProduct(ProductInterface $product, $andFlush = false)
    {
        $this->dm->persist($product);
        if ($andFlush) $this->flush();
    }

    /**
     * Update a Product that has been persisted and optionally flush that link.
     * Systems that allow for a delayed flush can use the $andFlush parameter, other
     * systems would disregard the flag. The success of the process is returned.
     *
     * @param Vespolina\Entity\ProductInterface $product
     *
     * @param boolean $andFlush
     */
    function updateProduct(ProductInterface $product, $andFlush = false)
    {
        $this->dm->persist($product);
        if ($andFlush) $this->flush();
    }


}
