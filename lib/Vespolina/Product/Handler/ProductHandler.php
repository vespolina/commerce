<?php
/**
 * (c) 2012 Vespolina Project http://www.vespolina-project.org
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */
namespace Vespolina\Product\Handler;

use Vespolina\Product\Handler\ProductHandlerInterface;
use Vespolina\Entity\Product\ProductInterface;

abstract class ProductHandler implements ProductHandlerInterface
{
    protected $productClass;
    protected $type = 'default';

    public function __construct($productClass)
    {
        $interfaceFQCN = 'Vespolina\Entity\Product\ProductInterface';
        if (!in_array($interfaceFQCN, class_implements($productClass))) {
            throw new \Exception('Please have your product implement interface '.$interfaceFQCN);
        }

        $this->productClass = $productClass;
    }

    public function createProduct($parent = null)
    {
        $product = $this->productClass();
        $product->setParent($parent);

        return $product;
    }

    public function getType()
    {
        return $this->type;
    }
}
