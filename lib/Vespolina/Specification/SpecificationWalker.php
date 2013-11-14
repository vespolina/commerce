<?php

/**
 * (c) 2011 - ∞ Vespolina Project http://www.vespolina-project.org
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Vespolina\Specification;

use Vespolina\Specification\SpecificationInterface;

/**
 * The specification walker is used to build the specification query by iterating over visitors
 * to detect which one supports the given specification and to apply the found visitor
 *
 * @author Daniel Kucharski <daniel@xerias.be>
 */
class SpecificationWalker
{
    protected $visitors;

    /**
     * @param array $visitors A list of visitors implementing the SpecificationVisitorInterface
     */
    public function __construct(array $visitors = array()) {

        $this->visitors = $visitors;
    }

    /**
     * Apply known specification visitors to the supplied specification to build the query
     *
     * @param SpecificationInterface $specification
     * @param $query
     */
    public function walk(SpecificationInterface $specification, $query)
    {
        foreach ($this->visitors as $visitor) {
            if ($visitor->supports($specification)) {
                $visitor->visit($specification, $this, $query);
            }
        }
    }

    /**
     * Add a new specification visitor
     *
     * @param SpecificationVisitorInterface $visitor
     */
    public function addVisitor(SpecificationVisitorInterface $visitor)
    {
        $this->visitors[] = $visitor;
    }
}