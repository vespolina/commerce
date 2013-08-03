<?php

/**
 * (c) 2011 - ∞ Vespolina Project http://www.vespolina-project.org
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Vespolina\Product\Specification;

use Vespolina\Product\Specification\SpecificationInterface;

class BaseSpecification
{

    protected $operands;

    public function __construct()
    {
        $this->operands = array();
    }

    protected function addOperand(SpecificationInterface $spec)
    {
        $this->operands[] = $spec;
    }
}
