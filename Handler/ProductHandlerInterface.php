<?php
/**
 * (c) 2012 Vespolina Project http://www.vespolina-project.org
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */
namespace Vespolina\ProductBundle\Handler;

use Vespolina\ProductBundle\Model\ProductInterface;

interface ProductHandlerInterface
{
    /**
     * Return a newly created product type for the handler
     *
     * @return Vespolina\ProductBundle\Model\ProductInterface
     */
    function createProduct();

    /**
     * Return the product type
     *
     * @return string
     */
    function getType();
}
