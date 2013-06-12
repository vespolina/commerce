<?php

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
