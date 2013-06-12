<?php

namespace Vespolina\Product\Specification;

use Vespolina\Entity\Product\ProductInterface;
use Vespolina\Product\Specification\SpecificationInterface;

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

    public function isSatisfiedBy(ProductInterface $product)
    {
        return $product->{"get" . $this->field}() === $this->value;
    }
}