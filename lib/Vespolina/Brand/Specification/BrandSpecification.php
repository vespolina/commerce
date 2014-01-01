<?php

/**
 * (c) 2011 - ∞ Vespolina Project http://www.vespolina-project.org
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Vespolina\Brand\Specification;

use Vespolina\Entity\Brand\BrandInterface;
use Vespolina\Specification\BaseSpecification;
use Vespolina\Brand\Specification\BrandSpecificationInterface;

/**
 * A brand specification implementing typical criterias used to query brands
 *
 * Functionally each additional criteria is to be seen as an AND specification
 *
 * Supports amongst:
 *  - brand attributes
 *
 * @author Daniel Kucharski <daniel@xerias.be>
 */
class BrandSpecification extends BaseSpecification implements BrandSpecificationInterface
{

}
