<?php

namespace Vespolina\Brand\Manager;

use Vespolina\Brand\Gateway\BrandGatewayInterface;
use Vespolina\Entity\Brand\BrandInterface;

use Vespolina\Specification\SpecificationInterface;


class BrandManager 
{
    protected $gateway;

    public function __construct(BrandGatewayInterface $gateway)
    {
        $this->gateway = $gateway;
    }

    public function findAll(SpecificationInterface $specification)
    {
        return $this->gateway->findAll($specification);
    }

    /**
     * @inheritdoc
     */
    public function deleteBrand(BrandInterface $brand, $andFlush = false)
    {
        $this->gateway->deleteBrand($brand, $andFlush);
    }

    /**
     * @inheritdoc
     */
    public function updateBrand(BrandInterface $brand, $andFlush = false)
    {
        $this->gateway->updateBrand($brand, $andFlush);
    }
} 