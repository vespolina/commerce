<?php

/**
 * (c) 2011 - ∞ Vespolina Project http://www.vespolina-project.org
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Vespolina\Product\Specification;

use Vespolina\Entity\Product\ProductInterface;
use Vespolina\Product\Specification\SpecificationInterface;

class PriceSpecification implements SpecificationInterface
{
    protected $field;
    protected $fromValue;
    protected $toValue;

    public function __construct($field, $fromValue, $toValue = null)
    {
        $this->$fromValue = $fromValue;
        $this->toValue = $toValue;
    }

    public function isSatisfiedBy(ProductInterface $product)
    {
        return $product->{"get" . $this->field}() === $this->value;
    }
}