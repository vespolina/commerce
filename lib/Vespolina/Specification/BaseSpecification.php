<?php

/**
 * (c) 2011 - ∞ Vespolina Project http://www.vespolina-project.org
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Vespolina\Specification;

use Vespolina\Specification\SpecificationInterface;

class BaseSpecification
{
    protected $operands;

    public function __construct()
    {
        $this->operands = array();
    }

    public function equals($name, $value)
    {
        $this->addOperand(new FilterSpecification($name, $value));

        return $this;
    }

    public function isSatisfiedBy($product)
    {
        foreach ($this->operands as $specification) {
            if (!$specification->isSatisfiedBy($product)) {

                return false;
            }
        }

        return true;
    }

    public function getOperands()
    {
        return $this->operands;
    }

    public function withHydration($value)
    {
        $this->addOperand(new HydrationSpecification($value));

        return $this;
    }

    protected function addOperand(SpecificationInterface $spec)
    {
        $this->operands[] = $spec;
    }
}
