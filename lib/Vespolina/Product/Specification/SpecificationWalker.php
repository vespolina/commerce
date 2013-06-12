<?php

namespace Vespolina\Product\Specification;
use Vespolina\Product\Specification\SpecificationInterface;

class SpecificationWalker
{
    protected $visitors;

    public function __construct(array $visitors = array()) {

        $this->visitors = $visitors;
    }

    public function walk(SpecificationInterface $specification, $query)
    {
        foreach ($this->visitors as $visitor) {
            if ($visitor->supports($specification)) {
                $visitor->visit($specification, $this, $query);
            }
        }
    }

    public function addVisitor(SpecificationVisitorInterface $visitor)
    {
        $this->visitors[] = $visitor;
    }
}
