<?php

/**
 * (c) 2011 - ∞ Vespolina Project http://www.vespolina-project.org
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Vespolina\Product\Specification;

use Vespolina\Entity\Brand\BrandInterface;
use Vespolina\Specification\SpecificationInterface;

class BrandSpecification implements SpecificationInterface
{
    protected $brand;

    public function __construct(BrandInterface $brand)
    {
        $this->brand = $brand;
    }

    public function getBrand()
    {
        return $this->brand;
    }

    public function equals($name, $value)
    {
        $this->nodeName = $value;
    }

    public function isSatisfiedBy($product)
    {
        return $product->{"get" . $this->field}() === $this->value;
    }
}