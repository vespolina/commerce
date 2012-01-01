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
    protected $mediaManager;

    public function __construct($identifiers, $identifierSetClass, $mediaManager = null)
    {
        $this->identifiers = $identifiers;
        $this->identifierSetClass = $identifierSetClass;
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
}
