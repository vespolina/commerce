<?php

namespace Vespolina\Product\Gateway;

use Molino\SelectQueryInterface;
use Vespolina\Entity\Product\ProductInterface;

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

    /**
     * Find a Product by the value in a field or combination of fields
     *
     * @param \Gateway\QueryInterface $query
     *
     * @return an instance of Vespolina\Entity\ProductInterface or an array of instances of Vespolina\Entity\ProductInterface
     */
    function findProduct(SelectQueryInterface $query = null);

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
