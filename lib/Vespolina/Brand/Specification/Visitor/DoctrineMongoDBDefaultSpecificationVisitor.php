<?php

/**
 * (c) 2011 - ∞ Vespolina Project http://www.vespolina-project.org
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Vespolina\Brand\Specification\Visitor;

use Vespolina\Specification\SpecificationWalker;
use Vespolina\Specification\Visitor\BaseDoctrineMongoDBDefaultSpecificationVisitor;

class DoctrineMongoDBDefaultSpecificationVisitor extends BaseDoctrineMongoDBDefaultSpecificationVisitor
{
    protected $methods = array(
        'AndSpecification' => 'visitAnd',
        'FilterSpecification' => 'visitFilter',
        'IdSpecification'   => 'visitId',
    );

    protected $filterMap = array(
        '=' => 'equals'
    );

    public function visitBrand(BrandSpecificationInterface $specification, SpecificationWalker $walker, $query)
    {
        //Retrieve all child specifications of the product
        foreach($specification->getOperands() as $operandSpecification) {

            if ($this->supports($operandSpecification)) {
                $this->visit($operandSpecification, $walker, $query);
            }
        }
    }
}
