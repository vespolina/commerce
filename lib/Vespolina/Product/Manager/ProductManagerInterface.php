<?php
/**
 * (c) 2011-2012 Vespolina Project http://www.vespolina-project.org
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */
namespace Vespolina\Product\Manager;

use Vespolina\Product\Handler\ProductHandlerInterface;
use Vespolina\Entity\Product\AttributeInterface;
use Vespolina\Entity\Product\MerchandiseInterface;
use Vespolina\Entity\Product\OptionGroupInterface;
use Vespolina\Entity\Product\ProductInterface;
use Vespolina\Entity\Identitifer\IdentifierInterface;

use Vespolina\Product\Specification\SpecificationInterface;
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
     * @param \Vespolina\Entity\Product\ProductInterface $product
     * @param string | \Vespolina\Entity\OptionInterface $type
     * @param $value
     */
    function addOptionToProduct(ProductInterface $product, $type, $value = null);

    /**
     * Add an Identifier to a product. If the options are passed in, the Identifier applies only to those specific
     * option types and values. The array of options can be Option objects or arrays with keys of 'type' and 'value'.
     *
     * @param \Vespolina\Entity\Product\ProductInterface $product
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
     * Create a product attribute.
     *
     * @param string $type
     * @param string $name
     *
     * @return \Vespolina\Entity\Product\AttributeInterface
     */
    function createAttribute($type, $name);

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
     * @param string $type Parent product (in case of a product bundle)
     *
     * @return \Vespolina\Entity\ProductInterface
     */
    function createProduct($type = 'default', $parent = null);

    /**
     * Find a collection of products or merchandise by a specification
     *
     * @param SpecificationInterface $specification
     *
     * @return array
     */
    function findAll(SpecificationInterface $specification);

    function findOne(SpecificationInterface $specification);


    /**
     * Find a Product by its object identifier
     *
     * @param $id
     *
     * @return \Vespolina\Entity\Product\ProductInterface
     */
    function findProductById($id);

    /**
     * Find a Product by an identifier node
     *
     * @param $name
     * @param $code
     *
     * @return \Vespolina\Entity\Product\ProductInterface
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
     * Find a collection of option groups by the criteria
     *
     * @param array $criteria
     * @param mixed $orderBy
     * @param mixed $limit
     * @param mixed $offset
     *
     * @return array
     */
    function findOptionGroupsBy(array $criteria, array $orderBy = null, $limit = null, $offset = null);

    /**
     * Find an OptionGroup by its object identifier
     *
     * @param $id
     * @return \Vespolina\Entity\Product\OptionGroupInterface
     */
    function findOptionGroupById($id);

    /**
     * Delete an OptionGroup with the passed  object identifier
     *
     * @param $id
     * @param Boolean $andFlush Whether to flush the changes (default true)
     */
    function deleteOptionGroupById($id, $andFlush = true);

    /**
     * Delete a persisted object
     *
     * @param \Vespolina\Entity\Product\OptionGroupInterface $optionGroup
     * @param Boolean $andFlush Whether to flush the changes (default true)
     */
    function deleteOptionGroup(OptionGroupInterface $optionGroup, $andPersist = true);

    /**
     * Update and persist
     *
     * @param \Vespolina\Entity\Product\OptionGroupInterface $optionGroup
     * @param Boolean $andFlush Whether to flush the changes (default true)
     */
    function updateOptionGroup(OptionGroupInterface $optionGroup, $andPersist = true);

    function updateMerchandise(MerchandiseInterface $merchandise, $andPersist = true);

    /**
     * Update and persist the product
     *
     * @param \Vespolina\Entity\Product\ProductInterface $product
     * @param Boolean $andFlush Whether to flush the changes (default true)
     */
    function updateProduct(ProductInterface $product, $andPersist = true);

    /**
     * Delete and persist the product
     *
     * @param \Vespolina\Entity\Product\ProductInterface $product
     * @param Boolean $andFlush Whether to flush the changes (default true)
     */
    function deleteProduct(ProductInterface $product, $andPersist = true);
}
