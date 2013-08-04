<?php

/**
 * (c) 2011 - ∞ Vespolina Project http://www.vespolina-project.org
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Vespolina\Product\Handler;

use Vespolina\Entity\Product\ProductInterface;

/**
 * Defines the interface for a product handler allowing to adjust interaction with the Vespolina system
 * on a per product type basis
 *
 * @author Daniel Kucharski <daniel@xerias.be>
 * @author Richard Shank <develop@zestic.com>
 */
interface ProductHandlerInterface
{
    /**
     * Return a newly created product type for the handler
     *
     * @return \Vespolina\Entity\Product\ProductInterface
     */
    function createProduct($parent = null);

    /**
     * Return the product type
     *
     * @return string
     */
    function getType();

    /**
     * Retrieve search data to be used by the indexing mechanism
     *
     * @return array Product data to be supplied to the indexing mechanism
     */
    function getSearchTerms(ProductInterface $product);
}
