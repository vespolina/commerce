<?php

/**
 * (c) 2011 - ∞ Vespolina Project http://www.vespolina-project.org
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Vespolina\Product\Specification\Visitor;

use Vespolina\Entity\Product\ProductInterface;
use Vespolina\Product\Specification\SpecificationVisitorInterface;
use Vespolina\Specification\SpecificationInterface;
use Vespolina\Product\Specification\SpecificationWalker;
use Vespolina\Product\Specification\ProductSpecificationInterface;

class DoctrinePHPCRDefaultSpecificationVisitor implements SpecificationVisitorInterface
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
        '=' => 'eq'
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
        $query->expr()->eq('id', $specification->getId());
    }

    public function visitFilter(SpecificationInterface $specification, SpecificationWalker $walker, $query)
    {
        $mappedOperator = $this->filterMap[$specification->getOperator()];
        $query->expr()->$mappedOperator($specification->getField(), $specification->getValue());
    }

    public function visitTaxonomyNode(SpecificationInterface $specification, SpecificationWalker $walker, $query)
    {
        $taxonomyNode = $specification->getTaxonomyNode();

        //Do we already have the taxonomy node?
        if (null != $taxonomyNode) {
            $query->from($taxonomyNode);
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
