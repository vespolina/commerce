<?php

/**
 * (c) 2011 - ∞ Vespolina Project http://www.vespolina-project.org
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Vespolina\Specification;

use Vespolina\Entity\Product\ProductInterface;
use Vespolina\Specification\SpecificationInterface;
use Vespolina\Specification\SpecificationWalker;

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
