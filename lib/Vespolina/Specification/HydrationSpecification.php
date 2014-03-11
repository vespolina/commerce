<?php

/**
 * (c) 2014 - ∞ Vespolina Project http://www.vespolina-project.org
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Vespolina\Specification;

use Vespolina\Specification\SpecificationInterface;

class HydrationSpecification implements SpecificationInterface
{
    protected $value;

    public function __construct($value)
    {
        $this->value = $value;
    }

    public function isSatisfiedBy($product)
    {
        return true;
    }

    public function getValue()
    {
        return $this->value;
    }
}