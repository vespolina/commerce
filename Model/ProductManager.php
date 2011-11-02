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
use Vespolina\ProductBundle\Model\Identifier\ProductIdentifierSet;
use Vespolina\ProductBundle\Model\Identifier\ProductIdentifierSetInterface;

/**
 * @author Richard Shank <develop@zestic.com>
 */
abstract class ProductManager implements ProductManagerInterface
{
    protected $identifiers;
    protected $identifierSetClass;
    protected $primaryIdentifier;
    protected $primaryIdentifierLabel;
    protected $mediaManager;

    public function __construct($identifiers, $identifierSetClass, $primaryIdentifier, $primaryIdentifierLabel = null, $mediaManager = null)
    {
//  $primaryIdentifierLabel = $this->container->getParameter('vespolina_project.primary_identifier.label'))
        $this->identifiers = $identifiers;
        $this->identifierSetClass = $identifierSetClass;

        if (!isset($this->identifiers[$primaryIdentifier])) {
            throw new \InvalidConfigurationException(
                sprintf('The product identifier %s has not been set in the vespolina_product configuration', $primaryIdentifier)
            );
        }
        $this->primaryIdentifier = $this->identifiers[$primaryIdentifier];
        if (!$primaryIdentifierLabel) {
            $identifier = new $this->primaryIdentifier;
            $primaryIdentifierLabel = $identifier->getName();
        }
        $this->primaryIdentifierLabel = $primaryIdentifierLabel;
        $this->mediaManager = $mediaManager;
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

    public function createPrimaryIdentifier()
    {
        return new $this->primaryIdentifier;
    }

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
    public function getIdentifierSetClass()
    {
        return $this->identifierSetClass;
    }

    /**
     * @inheritdoc
     */
    public function getMediaManager()
    {
        if (!$this->mediaManager) {
            throw new \ConfigurationException('The MediaManager has not been configured for the Vespolina ProductBundle');
        }
        return $this->mediaManager;
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
    public function getPrimaryIdentifier()
    {
        return $this->primaryIdentifier;
    }

    /**
     * @inheritdoc
     */
    public function getPrimaryIdentifierLabel()
    {
        return $this->primaryIdentifierLabel;
    }

    /**
     * @inheritdoc
     */
    public function addIdentifierSetToProduct(ProductIdentifierSetInterface $identifierSet, ProductInterface &$product)
    {
        $index = $this->getIdentifierSetIndex($identifierSet);
        $product->addIdentifierSet($index, $identifierSet);
    }

    /**
     * @inheritdoc
     */
    public function removeIdentifierSetFromProduct($identifierSet, ProductInterface &$product)
    {
        if (!is_string($identifierSet)) {
            $identifierSet = $this->getIdentifierSetIndex($identifierSet);
        }
        $product->removeIdentifierSet($identifierSet);
    }

    protected function getIdentifierSetIndex($identifierSet)
    {
        $index = null;
        $primaryIdentifier = $this->getPrimaryIdentifier();
        foreach ($identifierSet->getIdentifiers() as $node) {
            if ($node instanceof $primaryIdentifier) {
                $index = $node->getCode();
            }
        }
        if (!$index) {
            throw new \UnexpectedValueException(
                'The primary identifier is not in this product identifier set'
            );
        }
        return $index;
    }
}
