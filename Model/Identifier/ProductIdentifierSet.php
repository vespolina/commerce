<?php
/**
 * (c) Vespolina Project http://www.vespolina-project.org
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */
namespace Vespolina\ProductBundle\Model\Identifier;

use Vespolina\ProductBundle\Model\Identifier\IdentifierInterface;
use Vespolina\ProductBundle\Model\Identifier\ProductIdentifierSetInterface;
use Vespolina\ProductBundle\Model\Option\OptionSetInterface;

/**
 * @author Richard D Shank <develop@zestic.com>
 */
abstract class ProductIdentifierSet implements ProductIdentifierSetInterface
{
    protected $options;
    protected $optionClass;

    public function __construct($optionClass)
    {
        $this->optionClass = $optionClass;
        $this->options = new $optionClass;
    }

    /*
     * @inheritdoc
     */
    public function addOption(OptionInterface $option)
    {
        $this->options->addOption($option);
    }

    /*
     * @inheritdoc
     */
    public function removeOption(OptionInterface $option)
    {
        $this->options->removeOption($option);
    }

    /*
     * @inheritdoc
     */
    public function setOptions(OptionSetInterface $options)
    {
        $this->options = $options;
    }

    /**
     * @inheritdoc
     */
    public function getOptions()
    {
        return $this->options->getOptions();
    }

    /**
     * @inheritdoc
     */
    public function getOptionSet()
    {
        return $this->options;
    }

    /**
     * @inheritdoc
     */
    public function removeOptions()
    {
        $this->options = new $this->optionClass;
    }

    /*
     * @inheritdoc
     */
    public function addIdentifier(IdentifierNodeInterface $identifier)
    {
        $this->addChild($identifier);
    }

    /**
     * @inheritdoc
     */
    public function clearIdentifiers()
    {
        $this->clearChildren();
    }

    /**
     * @inheritdoc
     */
    public function getIdentifiers()
    {
        return $this->getChildren();
    }

    /**
     * @inheritdoc
     */
    public function setIdentifiers($identifiers)
    {
        $this->setChildren($identifiers);
    }

    /**
     * @inheritdoc
     */
    public function removeIdentifier(IdentifierNodeInterface $identifier)
    {
        $this->removeChild($identifier->getName());
    }

    public function __toString()
    {
        return 'ProductIdentifierSet';
    }
}
