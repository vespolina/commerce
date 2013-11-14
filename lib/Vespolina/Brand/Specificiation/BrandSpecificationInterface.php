<?php

/**
 * (c) 2011 - ∞ Vespolina Project http://www.vespolina-project.org
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Vespolina\Brand\Specification;

use Vespolina\Entity\Brand\BrandInterface;
use Vespolina\Specification\SpecificationInterface;
/**
 * Interface to construct a brand query
 *
 * @author Daniel Kucharski <daniel@xerias.be>
 */
interface BrandSpecificationInterface extends SpecificationInterface
{
    /**
     * Match brands having the attribute $name equal to $value
     *
     * @param $name
     * @param $value
     * @return mixed
     */
    function attributeEquals($name, $value);
}