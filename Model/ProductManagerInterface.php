<?php
/**
 * (c) Vespolina Project http://www.vespolina-project.org
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */
namespace Vespolina\ProductBundle\Model;

use Vespolina\ProductBundle\Model\ProductInterface;
use Vespolina\ProductBundle\Model\ProductManagerInterface;
use Vespolina\ProductBundle\Model\Identifier\IdentifierInterface;
use Vespolina\ProductBundle\Model\Identifier\ProductIdentifierSetInterface;
/**
 * @author Richard Shank <develop@zestic.com>
 */
interface ProductManagerInterface
{
    /**
     * Create a Product instance
     *
     * @return Vespolina\ProductBundle\Model\ProductInterface
     */
    function createProduct();

    /**
     * Create a ProductIdentifierSet from a PrimaryIdentifier
     *
     * @param Vespolina\ProductBundle\Model\Identifier\IdentifierInterface $identifier
     *
     * @return Vespolina\ProductBundle\Model\Identifier\ProductIdentifierSetInterface
     */
    function createIdentifierSet(IdentifierInterface $identifier);

    /**
     * Create a product identifier
     *
     * @param $name the name of the specific Product identifier
     *
     * @return IdentifierInterface
     */
    function createIdentifier($name);

    /**
     * Find a collection of products by the criteria
     *
     * @param array $criteria
     * @param mixed $orderBy
     * @param mixed $limit
     * @param mixed $offset
     *
     * @return array
     */
    function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null);

    /**
     * Find a Product by its object identifier
     *
     * @param $id
     * @return Vespolina\ProductBundle\Model\ProductInterface
     */
    function findProductById($id);

    /**
     * Find a Product by its slug
     *
     * @param $slug
     * @return Vespolina\ProductBundle\Model\ProductInterface
     */
    function findProductBySlug($slug);

    /**
     * Find a Product by an identifier node
     *
     * @param $name
     * @param $code
     *
     * @return Vespolina\ProductBundle\Model\ProductInterface
     */
    function findProductByIdentifier($name, $code);

    /**
     * Update and persist the product
     *
     * @param Vespolina\ProductBundle\Model\ProductInterface $product
     * @param Boolean $andFlush Whether to flush the changes (default true)
     */
    function updateProduct(ProductInterface $product, $andFlush = true);

    /**
     * Return the configured media manager for the ProductBundle
     *
     * @return service or null
     */
    function getMediaManager();
}
