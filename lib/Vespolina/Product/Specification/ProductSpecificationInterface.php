<?php

/**
 * (c) 2011 - ∞ Vespolina Project http://www.vespolina-project.org
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Vespolina\Product\Specification;

use Vespolina\Entity\Product\ProductInterface;
use Vespolina\Product\Specification\SpecificationInterface;
/**
 * Interface to construct a product query
 *
 * @author Daniel Kucharski <daniel@xerias.be>
 */
interface ProductSpecificationInterface extends SpecificationInterface
{
    /**
     * Does the supplied entity matches this specification?
     *
     * @param ProductInterface $product
     * @return boolean
     */
    function isSatisfiedBy(ProductInterface $product);

    /**
     * Match products having the attribute $name equal to $value
     *
     * @param $name
     * @param $value
     * @return mixed
     */
    function attributeEquals($name, $value);

    /**
     * Match products for which price element $name is between the supplied range
     *
     * @param $name Name of the price element
     * @param $fromValue
     * @param $toValue
     * @return mixed
     */
    function withPriceRange($name, $fromValue, $toValue);

}