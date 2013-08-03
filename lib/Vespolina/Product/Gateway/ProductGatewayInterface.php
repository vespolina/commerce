<?php

/**
 * (c) 2011 - ∞ Vespolina Project http://www.vespolina-project.org
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Vespolina\Product\Gateway;

use Vespolina\Entity\Product\ProductInterface;
use Vespolina\Product\Specification\SpecificationInterface;

/**
 * Defines the interface for a product gateway to persist and retrieve products
 *
 * The interface can be used for local gateways (eg. local mongo or orm database) but it might as well be
 * a remote ERP system
 *
 * @author Daniel Kucharski <daniel@xerias.be>
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
     * Find a product by it's ID and ID type.  If no type has been given the default id strategy will be chosen.
     *
     * @param $id
     * @param null $type
     * @return mixed
     */
    function matchProductById($id, $type = null);

    /**
     * Match multiple products against the supplied specification
     *
     * @param SpecificationInterface $specification
     * @return mixed
     */
    function findAll(SpecificationInterface $specification);

    /**
     * Find one product matching the requested specification
     *
     * @param SpecificationInterface $specification
     * @return mixed
     */
    function findOne(SpecificationInterface $specification);

    /**
     * Flush any changes to the gateway
     */
    function flush();

    /**
     * Persist a Product that has been created and optionally flush that link.
     * Systems that allow for a delayed flush can use the $andFlush parameter, other
     * systems would disregard the flag. The success of the process is returned.
     *
     * @param Vespolina\Entity\Product\ProductInterface $product
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
