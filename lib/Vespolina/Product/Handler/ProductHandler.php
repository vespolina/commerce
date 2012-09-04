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
        $this->productClass = $productClass;
    }

    public function createProduct()
    {
        return new $this->productClass();
    }

    public function getType()
    {
        return $this->type;
    }
}
