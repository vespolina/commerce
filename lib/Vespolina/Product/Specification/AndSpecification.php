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

class AndSpecification implements SpecificationInterface
{
    protected $operands = array();

    public function __construct()
    {
        $this->operands = func_get_args();
    }

    public function isSatisfiedBy(ProductInterface $product)
    {
        foreach ($this->operands as $specification) {
            if ( ! $specification->isSatisfiedBy($product)) {
                return false;
            }
        }

        return true;
    }
}