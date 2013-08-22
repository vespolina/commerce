<?php
/**
 * (c) 2011 - ∞ Vespolina Project http://www.vespolina-project.org
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */
namespace Vespolina\Product\Manager;

use Vespolina\Exception\InvalidConfigurationException;

use Vespolina\Entity\Channel\ChannelInterface;
use Vespolina\Entity\Product\AttributeInterface;
use Vespolina\Entity\Product\OptionGroupInterface;
use Vespolina\Entity\Product\ProductInterface;
use Vespolina\Entity\Identifier\IdentifierInterface;
use Vespolina\Product\Gateway\ProductGatewayInterface;
use Vespolina\Product\Handler\MerchandiseHandlerInterface;
use Vespolina\Product\Handler\ProductHandlerInterface;
use Vespolina\Product\Manager\ProductManagerInterface;
use Vespolina\Product\Specification\FilterSpecification;
use Vespolina\Product\Specification\IdSpecification;
use Vespolina\Specification\SpecificationInterface;

/**
 * @author Richard Shank <develop@zestic.com>
 * @author Daniel Kucharski <daniel@xerias.be>
 */
class ProductManager implements ProductManagerInterface
{
    protected $classMapping;
    protected $configuration;
    protected $gateways;
    protected $identifiers;
    protected $productHandlers;
    protected $merchandiseHandlers;
    protected $attributeClass;
    protected $merchandiseClass;
    protected $optionClass;
    protected $productClass;

    /**
     * Create a new product manager
     *
     * Pass in the default gateway, product class map and an optional configuration
     *
     * @param ProductGatewayInterface $defaultGateway
     * @param array $classMapping
     * @param array $configuration
     */
    public function __construct(ProductGatewayInterface $defaultGateway, array $classMapping, array $configuration = array())
    {
        //Prepare the class map
        $missingClasses = array();
        foreach (array('attribute', 'merchandise', 'option', 'product') as $class) {
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
        $this->classMapping = $classMapping;

        //Prepare the manager configuration
        $defaultConfiguration = array(
            'multiChannel' => false     //Switch off / on product merchandise support
        );

        $this->configuration = array_merge($configuration, $defaultConfiguration);

        //Setup the default product gateway
        $this->gateways = array('default' => $defaultGateway);
        $this->productHandlers = array();
    }

    public function addAttributeToProduct(ProductInterface $product, $attribute, $name = null)
    {
        if (!$attribute instanceof AttributeInterface) {
            if (is_array($attribute)) {
                $type = key($attribute);
                $name = $attribute[$type];
            } else {
                $type = $attribute;
            }
            $attribute  = new $this->attributeClass;
            $attribute->setType($type);
            $attribute->setName($name);
        }

        $product->addAttribute($attribute);
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
    public function addMerchandiseHandler(MerchandiseHandlerInterface $handler)
    {
        $type = $handler->getType();
        $this->merchandiseHandlers[$type] = $handler;
    }

    /**
     * @inheritdoc
     */
    public function createAttribute($type, $name)
    {
        /** @var $attribute \Vespolina\Entity\Product\AttributeInterface */
        $attribute = new $this->classMapping['attribute'];
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

    /**
     * @inheritdoc
     */
    public function createOptionGroup()
    {
        return new $this->classMapping['option'];
    }

    /**
     * @inheritdoc
     */
    public function createProduct($type = 'default', $parent = null)
    {
        if (isset($this->productHandlers[$type])) {

            return $this->productHandlers[$type]->createProduct($parent);
        }
        // TODO: this is a bit hacky, but it allows the legacy setup to work correctly until it can be updated to the handler

        if ($type !== 'default') {
            throw new \Exception(sprintf("%s is not a valid product type", $type));
        }
        $product = new $this->productClass($this->identifierSetClass);

        return $product;
    }

    /**
     * @inheritdoc
     */
    public function createMerchandise(ProductInterface $product, ChannelInterface $channel, $type = 'default')
    {
        if (isset($this->merchandiseHandlers[$type])) {

            $merchandise  =  $this->merchandiseHandlers[$type]->createMerchandise($product, $channel);

            //Build or adjust the link between the merchandise and its referencing product
            $this->merchandiseHandlers[$type]->link($merchandise, $product);
        }

        return $merchandise;
    }


    public function findAll(SpecificationInterface $specification) {

        return $this->resolveGateway()->findAll($specification);
    }

    public function findOne(SpecificationInterface $specification) {

        return $this->resolveGateway()->findOne($specification);
    }


    public function findProductById($id)
    {
        return $this->findOne(new IdSpecification($id));
    }

    public function findProductByIdentifier($name, $code)
    {

    }

    public function findProductBySlug($slug)
    {
        return $this->findOne(new FilterSpecification('slug', $slug));
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
        return '\Vespolina\Entity\Product\Option';
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
        $qb = $this->resolveGateway()->createQueryBuilder($this->optionGroupClass);
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
    public function deleteProduct(ProductInterface $product, $andPersist = true)
    {
        $this->resolveGateway($product)->deleteProduct($product);
    }

    /**
     * @inheritdoc
     */
    public function updateProduct(ProductInterface $product, $andPersist = true)
    {
        $this->resolveGateway($product)->updateProduct($product);
    }

    public function resolveGateway(ProductInterface $product = null, $name = 'default')
    {
        if (null != $product) {
            //Todo trigger an event to determine the product gateway based on the product data (eg. determine the gateway by an external id)
        }

        //Default fallback
        return $this->gateways[$name];
    }

    protected function doDeleteOptionGroup(OptionGroupInterface $merchandise)
    {

    }

    protected function doFindProductBySlug($slug)
    {

    }

    protected function doUpdateOptionGroup(OptionGroupInterface $optionGroup)
    {

    }
}
