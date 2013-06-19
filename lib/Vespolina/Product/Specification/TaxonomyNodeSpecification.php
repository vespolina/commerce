<?php

namespace Vespolina\Product\Specification;

use Vespolina\Entity\Product\ProductInterface;
use Vespolina\Entity\Taxonomy\TaxonomyNodeInterface;
use Vespolina\Product\Specification\SpecificationInterface;

class TaxonomyNodeSpecification implements SpecificationInterface
{
    protected $node;

    public function __construct(TaxonomyNodeInterface $node)
    {
        $this->node = $node;
    }

    public function isSatisfiedBy(ProductInterface $product)
    {
        foreach ($product->getTaxonomies() as $node) {

            if ($node->getName()  == $this->node->getName())
                return true;
        }
    }
}