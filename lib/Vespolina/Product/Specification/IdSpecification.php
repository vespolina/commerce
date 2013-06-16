<?php

namespace Vespolina\Product\Specification;

use Vespolina\Entity\Product\ProductInterface;
use Vespolina\Product\Specification\SpecificationInterface;

class IdSpecification implements SpecificationInterface
{
    protected $id;
    protected $type;


    public function __construct($id, $type = null)
    {
        $this->id = $id;
        $this->type = $type;
    }

    public function isSatisfiedBy(ProductInterface $product)
    {
        return $product->getId() === $this->id;
    }

    public function getId()
    {
        return $this->id;
    }
}