<?php

/**
 * (c) 2011 - ∞ Vespolina Project http://www.vespolina-project.org
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Vespolina\Specification;

use Vespolina\Specification\SpecificationInterface;

class FilterSpecification implements SpecificationInterface
{
    protected $field;
    protected $value;
    protected $operator;

    public function __construct($field, $value, $operator = '=')
    {
        $this->field = $field;
        $this->value = $value;
        $this->operator = $operator;
    }

    public function isSatisfiedBy($product)
    {
        return $product->{"get" . $this->field}() === $this->value;
    }

    public function getField()
    {
        return $this->field;
    }

    public function getValue()
    {
        return $this->value;
    }

    public function getOperator()
    {
        return $this->operator;
    }
}