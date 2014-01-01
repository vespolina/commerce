<?php

/**
 * (c) 2011 - ∞ Vespolina Project http://www.vespolina-project.org
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Vespolina\Specification\Visitor;

use Vespolina\Specification\SpecificationVisitorInterface;
use Vespolina\Specification\SpecificationInterface;
use Vespolina\Specification\SpecificationWalker;

class BaseDoctrineMongoDBDefaultSpecificationVisitor implements SpecificationVisitorInterface
{
    protected $methods = array(
        'AndSpecification' => 'visitAnd',
        'FilterSpecification' => 'visitFilter',
        'IdSpecification'   => 'visitId',
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
