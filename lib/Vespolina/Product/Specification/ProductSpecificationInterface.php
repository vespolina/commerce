<?php

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
    function isSatisfiedBy(ProductInterface $product);
}