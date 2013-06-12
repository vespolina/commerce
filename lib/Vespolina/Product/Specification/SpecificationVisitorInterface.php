<?php

namespace Vespolina\Product\Specification;

use Vespolina\Entity\Product\ProductInterface;
use Vespolina\Product\Specification\SpecificationWalker;

/**
 * Interface to construct a product query specification
 *
 * @author Daniel Kucharski <daniel@xerias.be>
 */
interface SpecificationVisitorInterface
{
    function supports(SpecificationInterface $specification);
    function visit(SpecificationInterface $specification, SpecificationWalker $walker, $query);
}
