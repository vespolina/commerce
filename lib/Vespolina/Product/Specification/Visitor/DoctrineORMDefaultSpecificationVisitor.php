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

class DoctrineORMDefaultSpecificationVisitor implements SpecificationVisitorInterface
{
    protected $lastParameterId;

    protected $methods = array(
        'AndSpecification' => 'visitAnd',
        'FilterSpecification' => 'visitFilter',
        'IdSpecification'   => 'visitId',
        'PriceSpecification' => 'visitPrice',
        'ProductSpecification' => 'visitProduct',
        'TaxonomyNodeSpecification' => 'visitTaxonomyNode'
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
        $this->andWhere($query, '=', 'id', $specification->getId());
    }

    public function visitFilter(SpecificationInterface $specification, SpecificationWalker $walker, $query)
    {
        $this->andWhere($query, $specification->getOperator(),$specification->getField(),$specification->getValue() );
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

    public function visitTaxonomyNode(SpecificationInterface $specification, SpecificationWalker $walker, $query)
    {
        $taxonomyNode = $specification->getTaxonomyNode();

        //Do we already have the taxonomy node?
        if (null != $taxonomyNode) {
           // $query->field('taxonomies')->equals($taxonomyNode);
            //If not we need to describe the taxonomy node
        } else {
            $rootAlias = $query->getRootAlias();

            $query->innerJoin($rootAlias . '.taxonomies', 't');
            $parameterId = $this->generateParameterId();
            $query->andWhere('t.name =?' . $parameterId);
            $query->setParameter($parameterId,  $specification->getName());
        }
    }

    protected function generateParameterId()
    {
        return ++$this->lastParameterId;
    }

    private function andWhere($queryBuilder, $operator, $field, $value)
    {
        $parameterId = $this->generateParameterId();
        $rootAlias = $queryBuilder->getRootAlias();
        $queryBuilder->andWhere(sprintf('%s.%s %s ?%d', $rootAlias, $field, $operator, $parameterId));
        $queryBuilder->setParameter($parameterId, $value);
    }
}
