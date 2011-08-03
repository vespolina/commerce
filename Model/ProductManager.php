<?php
/**
 * (c) Vespolina Project http://www.vespolina-project.org
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */
namespace Vespolina\ProductBundle\Model;

use Symfony\Component\DependencyInjection\Container;

use Vespolina\ProductBundle\Model\ProductInterface;
use Vespolina\ProductBundle\Model\ProductManagerInterface;
use Vespolina\ProductBundle\Model\Node\IdentifierNodeInterface;
use Vespolina\ProductBundle\Model\Node\ProductIdentifierSet;
use Vespolina\ProductBundle\Model\Node\ProductIdentifierSetInterface;

/**
 * @author Richard Shank <develop@zestic.com>
 */
abstract class ProductManager implements ProductManagerInterface
{
    protected $container;
    protected $identifiers;
    protected $primaryIdentifier;
    
    public function __construct(Container $container)
    {
        $this->container = $container;
        $this->identifiers = $container->getParameter('vespolina_product.product_manager.identifiers');
        $primaryIdentifierKey = $container->getParameter('vespolina_product.product_manager.primary_identifier');
        if (!$primaryIdentifierKey || !isset($this->identifiers[$primaryIdentifierKey])) {
            throw new \InvalidConfigurationException('vespolina_product.product_manager.primary_identifier must be set to one of the configured identifiers');
        }
        $this->primaryIdentifier = $this->identifiers[$primaryIdentifierKey];
    }
    
    /**
     * @inheritdoc
     */
    public function createIdentifierSet(IdentifierNodeInterface $identifier)
    {
        $productIdentifierSet = $this->container->getParameter('vespolina_product.model.product_identifier_set.class');
        $identifierSet = new $productIdentifierSet;
        $identifierSet->addIdentifier($identifier);
        return $identifierSet;
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
        if (!$label = $this->container->getParameter('vespolina_project.primary_identifier.label')) {
            $identifier = new $this->getPrimaryIdentifier();
            $label = $identifier->getName();
        }
        return $label;
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
