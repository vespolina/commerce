<?php

/**
 * (c) 2011 - ∞ Vespolina Project http://www.vespolina-project.org
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Vespolina\Product\Specification\Visitor;

use Vespolina\Entity\Product\ProductInterface;
use Vespolina\Product\Specification\ProductSpecificationInterface;
use Vespolina\Specification\SpecificationVisitorInterface;
use Vespolina\Specification\SpecificationInterface;
use Vespolina\Specification\SpecificationWalker;
use Vespolina\Specification\Visitor\BaseDoctrineMongoDBDefaultSpecificationVisitor;

class DoctrineMongoDBDefaultSpecificationVisitor extends BaseDoctrineMongoDBDefaultSpecificationVisitor implements SpecificationVisitorInterface
{
    protected $methods = array(
        'AndSpecification' => 'visitAnd',
        'BrandSpecification' => 'visitBrand',
        'FilterSpecification' => 'visitFilter',
        'IdSpecification'   => 'visitId',
        'PriceSpecification' => 'visitPrice',
        'ProductSpecification' => 'visitProduct',
        'TaxonomyNodeSpecification' => 'visitTaxonomyNode',
    );

    protected $filterMap = array(
        '=' => 'equals'
    );

    public function visitTaxonomyNode(SpecificationInterface $specification, SpecificationWalker $walker, $query)
    {
        $taxonomyNode = $specification->getTaxonomyNode();

        //Do we already have the taxonomy node?
        if (null != $taxonomyNode) {
            $query->field('taxonomies')->equals($taxonomyNode);
        //If not we need to describe the taxonomy node
        } else {

            //Todo: use taxonomy path instead of the slug
            $query->field('taxonomies.slug')->equals(strtolower($specification->getTaxonomyNodeName()));
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

    public function visitBrand(SpecificationInterface $specification, SpecificationWalker $walker, $query)
    {
        $id = $specification->getBrand()->getId();
        $brand = $specification->getBrand();
        $query->field('brands')->includesReferenceTo($brand);
//        $query->field('brand.id')->equals(new \MongoId($id));

    }
}
