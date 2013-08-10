<?php

/**
 * (c) 2011 - ∞ Vespolina Project http://www.vespolina-project.org
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Vespolina\Product\Specification;

use Vespolina\Entity\Product\ProductInterface;
use Vespolina\Entity\Taxonomy\TaxonomyNodeInterface;
use Vespolina\Specification\SpecificationInterface;

class TaxonomyNodeSpecification implements SpecificationInterface
{
    protected $node;
    protected $nodeName;

    public function __construct(TaxonomyNodeInterface $node = null)
    {
        $this->node = $node;
    }

    public function equals($name, $value)
    {
        $this->nodeName = $value;
    }

    public function getNodeName()
    {
        return $this->nodeName;
    }

    public function getTaxonomyNode()
    {
        return $this->node;
    }

    public function getTaxonomyNodeName()
    {
        if (null != $this->node) {
            $nodeName =  $this->node->getName();
        } else {
            $nodeName = $this->nodeName;
        }

        return $nodeName;
    }

    public function isSatisfiedBy($product)
    {
        $nodeName = $this->getTaxonomyNodeName();

        foreach ($product->getTaxonomies() as $node) {
            if ($node->getName()  == $nodeName)

                return true;
        }
    }
}