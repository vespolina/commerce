<?php

namespace Vespolina\Product\Specification;
use Vespolina\Entity\Taxonomy\TaxonomyNode;
use Vespolina\Entity\Taxonomy\TaxonomyNodeInterface;
use Vespolina\Product\Specification\ProductSpecificationInterface;
use Vespolina\Entity\Product\ProductInterface;

class ProductSpecification extends BaseSpecification implements ProductSpecificationInterface
{

    public function isSatisfiedBy(ProductInterface $product)
    {
        // TODO: Implement isSatisfiedBy() method.
    }

    public function attributeEquals($name, $value)
    {

        return $this;
    }
    public function attributeContains($name, $value)
    {

        return $this;
    }

    public function equals($name, $value)
    {
        $this->addOperand(new FilterSpecification($name, $value));

        return $this;
    }

    public function withPriceRange($name, $fromValue, $toValue)
    {
        $this->addOperand(new PriceSpecification($name, $fromValue, $toValue));

        return $this;
    }

    public function withTaxonomyNode(TaxonomyNodeInterface $node)
    {
        return $this;
    }
}
