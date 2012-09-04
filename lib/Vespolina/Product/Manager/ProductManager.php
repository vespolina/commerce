<?php
/**
 * (c) 2011-2012 Vespolina Project http://www.vespolina-project.org
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */
namespace Vespolina\ProductBundle\Model;

use Vespolina\ProductBundle\Handler\ProductHandlerInterface;
use Vespolina\Entity\ProductInterface;
use Vespolina\ProductBundle\Model\ProductManagerInterface;
use Vespolina\ProductBundle\Model\Identifier\IdentifierInterface;
use Vespolina\ProductBundle\Model\Identifier\ProductIdentifierSet;
use Vespolina\ProductBundle\Model\Identifier\ProductIdentifierSetInterface;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

/**
 * @author Richard Shank <develop@zestic.com>
 */
abstract class ProductManager implements ProductManagerInterface
{
    protected $productHandlers;
    protected $identifiers;
    protected $identifierSetClass;
    protected $productClass; // todo: remove after default product is created through a handler
    protected $assetManager;

    public function __construct($identifiers, $identifierSetClass, $assetManager)
    {
        $this->productHandlers = array();
        $this->identifiers = $identifiers;
        $this->identifierSetClass = $identifierSetClass;
        $this->assetManager = $assetManager;
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
    public function findOptionGroupsBy(array $criteria = array(), array $orderBy = null, $limit = null, $offset = null)
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
    function delete($object, $andFlush = true)
    {
        $this->dm->remove($object);
        if ($andFlush) {
            $this->dm->flush();
        }
    }

    /**
     * @inheritdoc
     */
    public function update($object, $andFlush = true)
    {
        $this->dm->persist($object);
        if ($andFlush) {
            $this->dm->flush();
        }
    }
}
