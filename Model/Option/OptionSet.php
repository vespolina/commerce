<?php
/**
 * (c) Vespolina Project http://www.vespolina-project.org
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */
namespace Vespolina\ProductBundle\Model\Option;

use Vespolina\ProductBundle\Model\Identifier\ProductIdentifierSetInterface;
use Vespolina\ProductBundle\Model\Option\OptionGroup;
use Vespolina\ProductBundle\Model\Option\OptionSetInterface;

/**
 * @author Richard D Shank <develop@zestic.com>
 */
abstract class OptionSet implements OptionSetInterface
{
    protected $optionGroups;
    protected $optionGroupClass;
    protected $identifierSet;

    public function __construct($optionGroupClass)
    {
        $this->optionGroupClass = $optionGroupClass;
    }

    /*
     * @inheritdoc
     */
    public function setIdentifierSet(ProductIdentifierSetInterface $identifierSet)
    {
        $this->identifierSet = $identifierSet;
    }

    /*
     * @inheritdoc
     */
    public function getIdentifierSet()
    {
        return $this->identifierSet;
    }

    /*
     * @inheritdoc
     */
    public function addOption(OptionInterface $option)
    {
        $type = $option->getType();
        if (!isset($this->optionGroups[$type])) {
            $this->optionGroups[$type] = $this->createOptionGroup();
        }
        $this->optionGroups[$type]->addOption($option);
    }

    /**
     * @inheritdoc
     */
    public function clearOptions()
    {
        $this->optionGroups = null;
    }

    /**
     * @inheritdoc
     */
    public function getOption($type, $value)
    {
        return isset($this->optionGroups[$type]) ? $this->optionGroups[$type]->getOption($value) : null;
    }

    /**
     * @inheritdoc
     */
    public function getOptionByDisplay($type, $display)
    {
        return isset($this->optionGroups[$type]) ? $this->optionGroups[$type]->getOptionByDisplay($display) : null;
    }

    /**
     * @inheritdoc
     */
    public function getOptions()
    {
        $options = array();
        foreach ($this->optionGroups as $group) {
            array_merge($options, $group->getOptions());
        }
        return $options;
    }

    /**
     * @inheritdoc
     */
    public function setOptions(array $options)
    {
        $this->optionGroups = null;
        foreach ($options as $option) {
            $this->addOption($option);
        }
    }

    /**
     * @inheritdoc
     */
    public function removeOption(OptionInterface $option, $removeGroup = true)
    {
        $type = $option->getType();
        if (isset($this->optionGroups[$type])) {
            $this->optionGroups[$type]->removeOption($option);
            if ($removeGroup && !sizeof($this->optionGroups[$type]->getOptions())) {
                unset($this->optionGroups[$type]);
            }
        }
    }

    /**
     * @inheritdoc
     */
    public function getOptionGroups()
    {
        return $this->optionGroups;
    }

    /**
     * @inheritdoc
     */
    public function getOptionGroup($name)
    {
        return isset($this->optionGroups[$name]) ? $this->optionGroups[$name] : null;
    }

    /**
     * @inheritdoc
     */
    public function setOptionGroups(array $groups)
    {
        $this->optionGroups = $groups;
    }

    /**
     * @inheritdoc
     */
    public function addOptionGroup(OptionGroupInterface $group)
    {
        $type = $group->getName();
        $this->optionGroups[$type] = $group;
    }

    /**
     * @inheritdoc
     */
    public function removeOptionGroup($group)
    {
        if ($group instanceof OptionGroupInterface) {
            $group = $group->getName();
        }
        unset($this->optionGroups[$group]);
    }

    protected function createOptionGroup()
    {
        return new $this->optionGroupClass;
    }
}
