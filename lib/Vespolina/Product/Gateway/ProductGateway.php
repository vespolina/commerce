<?php

namespace Vespolina\Product\Gateway;

use Vespolina\Entity\Product\ProductInterface;
use Vespolina\Exception\InvalidInterfaceException;
use Vespolina\Product\Specification\SpecificationInterface;
use Vespolina\Product\Specification\SpecificationWalker;
use Vespolina\Product\Specification\IdSpecification;

abstract class ProductGateway implements ProductGatewayInterface
{
    protected $productClass;
    protected $gatewayName;
    protected $specificationWalker;

    /**
     * @param string $managedClass
     */
    public function __construct($productClass, $gatewayName)
    {
        if (!class_exists($productClass) || !in_array('Vespolina\Entity\Product\ProductInterface', class_implements($productClass))) {
            throw new InvalidInterfaceException('Please have your product class implement Vespolina\Entity\Product\ProductInterface');
        }
        $this->productClass = $productClass;
        $this->gatewayName = $gatewayName;

    }

    public  function matchProductById($id, $type = null)
    {
        return $this->executeSpecification(new IdSpecification($id, $type), true);
    }

    public function findAll(SpecificationInterface $specification)
    {
        return $this->executeSpecification($specification);
    }

    public function findOne(SpecificationInterface $specification)
    {
        return $this->executeSpecification($specification, true);
    }

    protected function getSpecificationWalker()
    {
        if (null == $this->specificationWalker) {
            $defaultVisitorClass = 'Vespolina\Product\Specification\\Visitor\\' . $this->gatewayName . 'DefaultSpecificationVisitor';
            $this->specificationWalker = new SpecificationWalker(array(new $defaultVisitorClass()));
        }

        return $this->specificationWalker;
    }

}