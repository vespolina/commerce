<?php

namespace Vespolina\Product\Specification\Visitor;

use Vespolina\Entity\Product\ProductInterface;
use Vespolina\Product\Specification\SpecificationVisitorInterface;
use Vespolina\Product\Specification\SpecificationInterface;
use Vespolina\Product\Specification\SpecificationWalker;
use Vespolina\Product\Specification\ProductSpecificationInterface;

class DoctrineMongoDBDefaultSpecificationVisitor implements SpecificationVisitorInterface
{
    protected $methods = array(
        'AndSpecification' => 'visitAnd',
        'FilterSpecification' => 'visitFilter',
        'IdSpecification'   => 'visitId',
        'PriceSpecification' => 'visitPrice',
        'ProductSpecification' => 'visitProduct',
        'TaxonomyNodeSpecification' => 'visitTaxonomyNode',
    );

    protected $filterMap = array(
        '=' => 'equals'
    );

    public function supports(SpecificationInterface $specification)
    {
        $classPath = explode('\\', get_class($specification));

        return isset($this->methods[end($classPath)]);
    }

    public function visit(SpecificationInterface $specification, SpecificationWalker $walker, $query)
    {
        $classPath = explode('\\', get_class($specification));
        $method = $this->methods[end($classPath)];
        $this->$method($specification, $walker, $query);
    }

    public function visitId(SpecificationInterface $specification, SpecificationWalker $walker, $query)
    {
        $query->field('_id')->equals($specification->getId());
    }

    public function visitFilter(SpecificationInterface $specification, SpecificationWalker $walker, $query)
    {
        $mappedOperator = $this->filterMap[$specification->getOperator()];
        $query->field($specification->getField())->$mappedOperator($specification->getValue());
    }

    public function visitTaxonomyNode(SpecificationInterface $specification, SpecificationWalker $walker, $query)
    {
        $taxonomyNode = $specification->getTaxonomyNode();

        //Do we already have the taxonomy node?
        if (null != $taxonomyNode) {
            $query->field('taxonomies')->equals($taxonomyNode);
        //If not we need to describe the taxonomy node
        } else {
            $query->field('taxonomies.name')->equals($specification->getName());
        }
    }

    public function visitProduct(ProductSpecificationInterface $specification, SpecificationWalker $walker, $query)
    {
        //Retrieve all child specifications of the product
        foreach($specification->getOperands() as $operandSpecification) {

            if ($this->supports($operandSpecification)) {
                $this->visit($operandSpecification, $walker, $query);
            }
        }
    }

    protected function generateParameterId()
    {
        return ++$this->lastParameterId;
    }

    private function andWhere($comparison, $field, $value)
    {
        $parameterId = $this->generateParameterId();
        $rootAlias = $this->getQueryBuilder()->getRootAlias();
        $this->getQueryBuilder()->andWhere(sprintf('%s.%s %s ?%d', $rootAlias, $field, $comparison, $parameterId));
        $this->getQueryBuilder()->setParameter($parameterId, $value);
    }

}
