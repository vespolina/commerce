<?php

namespace Vespolina\Product\Specification;
use Vespolina\Product\Specification\SpecificationInterface;
use Vespolina\Entity\Product\ProductInterface;

class DoctrineMongoDBDefaultSpecificationVisitor implements SpecificationVisitorInterface
{

    private $methods = array(
        'EqualSpecification' => 'visitEqual',
        'AndSpecification' => 'visitAnd'
    );

    public function supports(SpecificationInterface $specification)
    {
        return isset($this->methods[get_class($specification)]);
    }

    public function visit(SpecificationInterface $specification, SpecificationWalker $walker, $query)
    {
        $method = $this->methods[get_class($specification)];
        $this->$method($specification, $walker, $query);
    }

    public function visitEqual(SpecificationInterface $specification, SpecificationWalker $walker, $query)
    {

    }

}
