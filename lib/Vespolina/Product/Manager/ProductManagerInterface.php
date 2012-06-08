<?php
/**
 * (c) 2011-2012 Vespolina Project http://www.vespolina-project.org
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */
namespace Vespolina\Product\Manager;

use Vespolina\ProductBundle\Handler\ProductHandlerInterface;
use Vespolina\Entity\ProductInterface;
use Vespolina\Entity\IdentifierInterface
;
use Vespolina\ProductBundle\Model\Identifier\ProductIdentifierSetInterface;
/**
 * @author Richard Shank <develop@zestic.com>
 */
interface ProductManagerInterface
{
    /**
     * Add an option to a product, the option type, like 'color' and value like 'blue' are passed in. If there is already
     * an Option instance of a particular type, the new value is added to the object, otherwise a new Option object is
     * created. Optionally, just an Option object can be passed in, without a third value parameter.
     *
     * @param \Vespolina\Entity\ProductInterface $product
     * @param $type string or Vespolina\Entity\OptionInterface
     * @param $value
     */
    function addOptionToProduct(ProductInterface $product, $type, $value = null);

    /**
     * Add an Identifier to a product. If the options are passed in, the Identifier applies only to those specific
     * option types and values. The array of options can be Option objects or arrays with keys of 'type' and 'value'.
     *
     * @param \Vespolina\Entity\ProductInterface $product
     * @param array|null $options
     */
    function addIdentifierToProduct(ProductInterface $product, array $options = null);

    /**
     * Add a product handler to the manager
     *
     * @param ProductHandlerInterface $handler
     */
    function addProductHandler(ProductHandlerInterface $handler);

    /**
     * Create a product identifier. This will be moved out of ProductManager in the future.
     *
     * @param $name the name of the specific Product identifier
     *
     * @return IdentifierInterface
     */
    function createIdentifier($name);

    function createOption($type, $value);

    /**
     * Create a Product instance
     *
     * @param string $type (optional)
     *
     * @return Vespolina\Entity\ProductInterface
     */
    function createProduct($type = 'default');

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
     * @return Vespolina\Entity\ProductInterface
     */
    function findProductById($id);

    /**
     * Find a Product by its slug
     *
     * @param $slug
     * @return Vespolina\Entity\ProductInterface
     */
    function findProductBySlug($slug);

    /**
     * Find a Product by an identifier node
     *
     * @param $name
     * @param $code
     *
     * @return Vespolina\Entity\ProductInterface
     */
    function findProductByIdentifier($name, $code);

    /**
     * Return a product handler object
     *
     * @param $type
     *
     * @returns HandlerInterface
     */
    function getProductHandler($type);

    /**
     * Return all of the product handlers
     *
     * @return array of HandlerInterface
     */
    function getProductHandlers();

    /**
     * Remove a product handler from the manager
     *
     * @param string $type
     */
    function removeProductHandler($type);

    /**
     * Update and persist the product
     *
     * @param Vespolina\Entity\ProductInterface $product
     * @param Boolean $andFlush Whether to flush the changes (default true)
     */
    function updateProduct(ProductInterface $product, $andFlush = true);
}
