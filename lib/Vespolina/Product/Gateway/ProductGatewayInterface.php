<?php

namespace Vespolina\Product\Gateway;

use Molino\SelectQueryInterface;
use Vespolina\Entity\Product\ProductInterface;
use Vespolina\Product\Specification\SpecificationInterface;

/**
 * @author Richard Shank <develop@zestic.com>
 */
interface ProductGatewayInterface
{

    /**
     * Delete a Product that has been persisted and optionally flush that link.
     * Systems that allow for a delayed flush can use the $andFlush parameter, other
     * systems would disregard the flag. The success of the process is returned.
     *
     * @param \Vespolina\Entity\ProductInterface $product
     *
     * @param boolean $andFlush
     */
    function deleteProduct(ProductInterface $product, $andFlush = false);


    function matchProductById($id, $type = null);

    /**
     * Match multiple products against the supplied specification
     *
     * @param SpecificationInterface $specification
     * @return mixed
     */
    function findAll(SpecificationInterface $specification);

    function findOne(SpecificationInterface $specification);

    /**
     * Flush any changes to the database
     */
    function flush();

    /**
     * Persist a Product that has been created and optionally flush that link.
     * Systems that allow for a delayed flush can use the $andFlush parameter, other
     * systems would disregard the flag. The success of the process is returned.
     *
     * @param Vespolina\Entity\ProductInterface $product
     *
     * @param boolean $andFlush
     */
    function persistProduct(ProductInterface $product, $andFlush = false);

    /**
     * Update a Product that has been persisted and optionally flush that link.
     * Systems that allow for a delayed flush can use the $andFlush parameter, other
     * systems would disregard the flag. The success of the process is returned.
     *
     * @param Vespolina\Entity\ProductInterface $product
     *
     * @param boolean $andFlush
     */
    function updateProduct(ProductInterface $product, $andFlush = false);
}
