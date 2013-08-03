<?php

/**
 * (c) 2011 - ∞ Vespolina Project http://www.vespolina-project.org
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Vespolina\Product\Gateway;

use Vespolina\Entity\Product\ProductInterface;
use Vespolina\Exception\InvalidInterfaceException;
use Vespolina\Product\Specification\SpecificationInterface;

/**
 * Defines a (session) memory product gateway , suitable for testing purposes
 *
 * @author Daniel Kucharski <daniel@xerias.be>
 * @author Richard Shank <develop@zestic.com>
 */
class ProductMemoryGateway extends ProductGateway
{
    protected $products;

    /**
     * @param $productClass
     */
    public function __construct($productClass)
    {
        parent::__construct($productClass, 'Memory');

        $this->products = array();
    }

    protected function executeSpecification(SpecificationInterface $specification, $matchOne = false)
    {
        $results = array();
        foreach ($this->products as $product)
        {
            if (!$specification->isSatisfiedBy($product)) {
               continue;
            }
            if ($matchOne) {
                return $product;
            }

            $results[] = $product;
        }

        return $results;
    }

    /**
     * @inheritdoc
     */
    function deleteProduct(ProductInterface $product, $andFlush = false)
    {
        foreach ($this->products as $id =>$memoryProduct) {
            if ($memoryProduct->equals($product)) {
                unset($this->products[$id]);

                return;
            }
        }
    }

    /**
     * @inheritdoc
     */
    function flush()
    {
        //Nothing is required to be flushed for this gateway
    }

    /**
     * @inheritdoc
     */
    function persistProduct(ProductInterface $product, $andFlush = false)
    {
        //Nothing is required to be flushed for this gateway
    }

    /**
     * @inheritdoc
     */
    function updateProduct(ProductInterface $product, $andFlush = false)
    {
        $id = $product->getId();

        if (null == $id) {
            $id = uniqid();
            $rp = new \ReflectionProperty($product, 'id');
            $rp->setAccessible(true);
            $rp->setValue($product, $id);
            $rp->setAccessible(false);
        }

        $this->products[$id] = $product;
    }


}
