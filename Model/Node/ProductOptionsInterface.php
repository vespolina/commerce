<?php
/**
 * (c) Vespolina Project http://www.vespolina-project.org
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */
namespace Vespolina\ProductBundle\Model\Node;

use Vespolina\ProductBundle\Model\ProductNodeInterface;
use Vespolina\ProductBundle\Model\Node\ProductNodeInterface;

/**
 * @author Richard D Shank <develop@zestic.com>
 */
interface ProductOptionsInterface extends ProductNodeInterface
{
    /**
     * Add a product to this product products node.
     *
     * @param Vespolina\ProductBundle\Model\Node\ProductNodeInterface $product
     */
    public function addProduct(ProductNodeInterface $product);

    /**
     * Clear all products from this product products
     */
    public function clearProducts();

    /**
     * Add a collection of products
     *
     * @param array $products
     */
    public function setProduct($products);

    /**
     * Remove a product from this product products set
     *
     * @param ProductNodeInterface $product
     */
    public function removeProduct(ProductNodeInterface $product);
}
