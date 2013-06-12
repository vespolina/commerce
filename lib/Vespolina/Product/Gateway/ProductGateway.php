<?php

namespace Vespolina\Product\Gateway;

use Vespolina\Entity\Product\ProductInterface;
use Vespolina\Exception\InvalidInterfaceException;
use Vespolina\Product\Specification\SpecificationInterface;
use Vespolina\Product\Specification\SpecificationWalker;

abstract class ProductGateway implements ProductGatewayInterface
{
    protected $productClass;
    protected $gatewayName;
    protected $specificationWalker;

    /**
     * @param \Molino\MolinoInterface $molino
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

    protected function buildSpecification(SpecificationInterface $specification)
    {
        $query = $this->createQuery();
        $this->getSpecificationWalker()->walk($specification, $query);

        return $query;
    }

    protected function executeSpecification(SpecificationInterface $specification)
    {
        $query = $this->buildSpecification($specification);
    }

    protected function getSpecificationWalker()
    {
        if (null == $this->specificationWalker) {

            $defaultVisitorClass = 'Vespolina\Product\Specification\\' . $this->gatewayName . 'DefaultSpecificationVisitor';
            $this->specificationWalker = new SpecificationWalker(array(new $defaultVisitorClass()));
        }

        return $this->specificationWalker;
    }

}
