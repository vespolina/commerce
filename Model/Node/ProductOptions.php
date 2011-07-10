<?php
/**
 * (c) Vespolina Project http://www.vespolina-project.org
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */
namespace Vespolina\ProductBundle\Model\Node;

use Vespolina\ProductBundle\Model\ProductNode;
use Vespolina\ProductBundle\Model\Node\ProductOptionsInterface;

/**
 * @author Richard D Shank <develop@zestic.com>
 */
class ProductOptions extends ProductNode implements ProductOptionsInterface
{
    /*
     * @inheritdoc
     */
    public function addProduct(ProductNodeInterface $product)
    {
        $this->addChild($product);
    }

    /**
     * @inheritdoc
     */
    public function clearProducts()
    {
        $this->clearChildren();
    }

    /**
     * @inheritdoc
     */
    public function setProduct($products)
    {
        $this->setChildren($products);
    }

    /**
     * @inheritdoc
     */
    public function removeProduct(ProductNodeInterface $product)
    {
        $this->removeChild($product->getName());
    }
}
