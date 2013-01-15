<?php
/**
 * (c) 2011-2012 Vespolina Project http://www.vespolina-project.org
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */
namespace Vespolina\Product\Manager;

use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

use Vespolina\Entity\Product\MerchandiseInterface;
use Vespolina\Entity\Product\OptionGroupInterface;
use Vespolina\Entity\Product\ProductInterface;
use Vespolina\Entity\Identifier\IdentifierInterface;
use Vespolina\Product\Handler\ProductHandlerInterface;
use Vespolina\Product\Manager\ProductManagerInterface;

/**
 * @author Richard Shank <develop@zestic.com>
 */
class ProductManager implements ProductManagerInterface
{
    protected $attributeClass;
    protected $identifiers;
    protected $merchandiseClass;
    protected $optionClass;
    protected $productClass;
    protected $productHandlers;

    public function __construct(ProductGateway $gateway, array $classMapping)
    {
        $missingClasses = array();
        foreach (array('Attribute', 'Merchandise', 'Option', 'Product') as $class) {
            $class = $class . 'Class';
            if (isset($classMapping[$class])) {

                if (!class_exists($classMapping[$class]))
                    throw new InvalidConfigurationException(sprintf("Class '%s' not found as '%s'", $classMapping[$class], $class));

                $this->{$class} = $classMapping[$class];
                continue;
            }
            $missingClasses[] = $class;
        }

        if (count($missingClasses)) {
            throw new InvalidConfigurationException(sprintf("The following partner classes are missing from configuration: %s", join(', ', $missingClasses)));
        }
//        $this->identifiers = $identifiers;
        $this->productHandlers = array();
    }

    public function addOptionToProduct(ProductInterface $product, $type, $value = null)
    {

    }

    public function addIdentifierToProduct(ProductInterface $product, array $options = null)
    {

    }

    /**
     * @inheritdoc
     */
    public function addProductHandler(ProductHandlerInterface $handler)
    {
        $type = $handler->getType();
        $this->productHandlers[$type] = $handler;
    }

    /**
     * @inheritdoc
     */
    public function createAttribute($type, $name)
    {
        /** @var $attribute \Vespolina\Entity\Product\AttributeInterface */
        $attribute = new $this->attributeClass;
        $attribute->setType($type);
        $attribute->setName($name);

        return $attribute;
    }

    /**
     * @inheritdoc
     */
    public function createIdentifierSet(IdentifierInterface $identifier)
    {
        $productIdentifierSet = $this->getIdentifierSetClass();
        $identifierSet = new $productIdentifierSet;
        $identifierSet->addIdentifier($identifier);

        return $identifierSet;
    }

    /**
     * @inheritdoc
     */
    public function createIdentifier($name)
    {
        $name = strtolower($name);
        return new $this->identifiers[$name];
    }

    public function createMerchandise(ProductInterface $product)
    {
        return new $this->merchandiseClass($product);
    }

    /**
     * @inheritdoc
     */
    public function createOption($type, $value)
    {
        $optionClass = $this->getOptionClass();
        $option = new $optionClass;
        $option->setType($type);
        $option->setValue($value);

        return $option;
    }

    public function createOptionGroup()
    {
        return new $this->optionGroupClass;
    }

    /**
     * @inheritdoc
     */
    public function createProduct($type = 'default')
    {
        if (isset($this->productHandlers[$type])) {

            return $this->productHandlers[$type]->createProduct();
        }
        // TODO: this is a bit hacky, but it allows the legacy setup to work correctly until it can be updated to the handler

        if ($type !== 'default') {
            throw new \Exception(sprintf("%s is not a valid product type", $type));
        }
        $product = new $this->productClass($this->identifierSetClass);

        return $product;
    }

    public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        return $this->doFindBy($criteria, $orderBy, $limit, $offset);
    }


    public function findProductById($id)
    {
        return $this->doFindProductById($id);
    }

    public function findProductByIdentifier($name, $code)
    {

    }

    public function findProductBySlug($slug)
    {
        return $this->doFindProductBySlug($slug);
    }

    public function findMerchandiseBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        return $this->doFindMerchandiseBy($criteria, $orderBy, $limit, $offset);
    }

    public function findMerchandiseByTerms(array $terms)
    {
        return $this->doFindMerchandiseByTerms($terms);
    }

    public function getAssetManager()
    {
        return $this->assetManager;
    }

    /**
     * @inheritdoc
     */
    public function getIdentifierSetClass()
    {
        return $this->identifierSetClass;
    }

    /**
     * @inheritdoc
     */
    public function getMerchandise(array $constraints = null)
    {
        return $this->doGetMerchandise($constraints);
    }

    /**
     * @inheritdoc
     */
    public function getOptionClass()
    {
        // TODO: make configurable
        return '\Vespolina\ProductBundle\Document\Option';
    }

    /**
     * @inheritdoc
     */
    public function getProductHandler($type)
    {
        if (isset($this->productHandlers[$type])) {
            return $this->productHandlers[$type];
        }

        return null;
    }

    /**
     * @inheritdoc
     */
    public function getProductHandlers()
    {
        return $this->productHandlers;
    }

    /**
     * @inheritdoc
     */
    public function removeProductHandler($type)
    {
        unset($this->productHandlers[$type]);
    }

    /**
     * @inheritdoc
     */
    public function findOptionGroupsBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        return $this->optionGroupRepo->findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * @inheritdoc
     */
    public function findOptionGroupsData(array $orderBy = null, $limit = null, $offset = null)
    {
        $qb = $this->dm->createQueryBuilder($this->optionGroupClass);
        $qb->hydrate(false);
        if($limit) {
            $qb->limit($limit);
        }
        if($offset) {
            $qb->skip($offset);
        }
        if($orderBy) {
            $qb->sort(key($orderBy), $orderBy);
        }

        return $qb->getQuery()
            ->execute();
    }

    /**
     * @inheritdoc
     */
    public function findOptionGroupById($id)
    {
        return $this->optionGroupRepo->find($id);
    }

    /**
     * @inheritdoc
     */
    function deleteOptionGroupById($id, $andFlush = true)
    {
        if ($group = $this->optionGroupRepo->find($id))
        {
            $this->dm->remove($group);
            if ($andFlush) {
                $this->dm->flush();
            }
        }
    }

    /**
     * @inheritdoc
     */
    function deleteOptionGroup(OptionGroupInterface $optionGroup, $andPersist = true)
    {
        if ($andPersist) {
            $this->doDeleteOptionGroup($optionGroup);
        }
    }

    /**
     * @inheritdoc
     */
    public function updateOptionGroup(OptionGroupInterface $optionGroup, $andPersist = true)
    {
        if ($andPersist) {
            $this->doUpdateOptionGroup($optionGroup);
        }
    }

    /**
     * @inheritdoc
     */
    public function updateMerchandise(MerchandiseInterface $merchandise, $andPersist = true)
    {
        if ($andPersist) {
            $this->doUpdateMerchandise($merchandise);
        }
    }

    /**
     * @inheritdoc
     */
    public function deleteProduct(ProductInterface $product, $andPersist = true)
    {
        $this->gateway->deleteProduct($product);
    }

    /**
     * @inheritdoc
     */
    public function updateProduct(ProductInterface $product, $andPersist = true)
    {
        $this->gateway->updateProduct($product);
    }

    protected function doDeleteOptionGroup(OptionGroupInterface $merchandise)
    {

    }

    protected function doFindProductBySlug($slug)
    {

    }

    protected function doFindMerchandiseBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {

    }

    protected function doFindMerchandiseByTerms(array $terms)
    {

    }

    protected function doGetMerchandise(array $constraints = null)
    {

    }

    protected function doUpdateMerchandise(MerchandiseInterface $optionGroup)
    {

    }

    protected function doUpdateOptionGroup(OptionGroupInterface $optionGroup)
    {

    }
}
