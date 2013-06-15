<?php

namespace Vespolina\Product\Gateway;

use Molino\MolinoInterface;
use Molino\SelectQueryInterface;
use Vespolina\Entity\Product\ProductInterface;
use Vespolina\Exception\InvalidInterfaceException;
use Vespolina\Product\Specification\SpecificationInterface;

class ProductDoctrineMongoDBGateway extends ProductGateway
{

    protected $documentManager;
    /**
     * @param string $managedClass
     */
    public function __construct($documentManager, $productClass)
    {
        $this->documentManager = $documentManager;
        parent::__construct($productClass, 'DoctrineMongoDB');
    }

    public function createQuery()
    {
        return $this->documentManager->createQueryBuilder($this->productClass);
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
        // TODO: Implement deleteProduct() method.
    }

    /**
     * Flush any changes to the database
     */
    function flush()
    {
        // TODO: Implement flush() method.
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
        // TODO: Implement persistProduct() method.
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
        $this->documentManager->persist($product);
        if ($andFlush) $this->documentManager->flush();
    }


}
