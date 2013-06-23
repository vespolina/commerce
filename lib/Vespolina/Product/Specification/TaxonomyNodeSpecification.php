<?php

namespace Vespolina\Product\Specification;

use Vespolina\Entity\Product\ProductInterface;
use Vespolina\Entity\Taxonomy\TaxonomyNodeInterface;
use Vespolina\Product\Specification\SpecificationInterface;

class TaxonomyNodeSpecification implements SpecificationInterface
{
    protected $node;
    protected $name;

    public function __construct(TaxonomyNodeInterface $node = null)
    {
        $this->node = $node;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getTaxonomyNode()
    {
        return $this->node;
    }

    public function equals($name, $value)
    {
        $this->name = $value;
    }

    public function isSatisfiedBy(ProductInterface $product)
    {
        if (null != $this->node) {
            $name =  $this->node->getName();
        } else {
            $name = $this->name;
        }
        foreach ($product->getTaxonomies() as $node) {
            if ($node->getName()  == $name)
                return true;

        }
    }
}