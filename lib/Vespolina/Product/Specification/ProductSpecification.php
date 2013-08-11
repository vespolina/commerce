<?php

/**
 * (c) 2011 - ∞ Vespolina Project http://www.vespolina-project.org
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Vespolina\Product\Specification;

use Vespolina\Entity\Channel\ChannelInterface;
use Vespolina\Entity\Product\ProductInterface;
use Vespolina\Entity\Taxonomy\TaxonomyNode;
use Vespolina\Entity\Taxonomy\TaxonomyNodeInterface;
use Vespolina\Product\Specification\ProductSpecificationInterface;
use Vespolina\Product\Specification\TaxonomyNodeSpecification;

/**
 * A product specification implementing typical criterias used to query products
 *
 * Functionally each additional criteria is to be seen as an AND specification
 *
 * Supports amongst:
 *  - product attributes
 *  - product price and range(s)
 *  - product taxonomy node(s)
 *
 * @author Daniel Kucharski <daniel@xerias.be>
 */
class ProductSpecification extends BaseSpecification implements ProductSpecificationInterface
{
    public function isSatisfiedBy($product)
    {
        foreach ($this->operands as $specification) {
            if (!$specification->isSatisfiedBy($product)) {

                return false;
            }
        }

        return true;
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

    public function getOperands()
    {
        return $this->operands;
    }

    public function withPriceRange($name, $fromValue, $toValue)
    {
        $this->addOperand(new PriceSpecification($name, $fromValue, $toValue));

        return $this;
    }

    public function withTaxonomyNode(TaxonomyNodeInterface $node)
    {
        $this->addOperand(new TaxonomyNodeSpecification($node));

        return $this;
    }

    public function withChannel(ChannelInterface $channel)
    {
        $this->addOperand(new ChannelSpecification($channel));

        return $this;
    }

    public function withTaxonomyNodeName($name)
    {
        $taxonomyNodeSpecification = new TaxonomyNodeSpecification();
        $taxonomyNodeSpecification->equals('name', $name);
        $this->addOperand($taxonomyNodeSpecification);

        return $this;
    }
}
