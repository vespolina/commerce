<?php
/**
 * (c) Vespolina Project http://www.vespolina-project.org
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */
namespace Vespolina\ProductBundle\Model\Option;

use Vespolina\ProductBundle\Model\Option\OptionGroup;
use Vespolina\ProductBundle\Model\Option\OptionSetInterface;

/**
 * @author Richard D Shank <develop@zestic.com>
 */
abstract class OptionSet implements OptionSetInterface
{
    protected $groups;
    protected $optionGroupClass;

    public function __construct($optionGroupClass)
    {
        $this->optionGroupClass = $optionGroupClass;
    }
    
    /*
     * @inheritdoc
     */
    public function addOption(OptionInterface $option)
    {
        $type = $option->getType();
        if (!isset($this->groups[$type])) {
            $this->groups[$type] = $this->createOptionGroup();
        }
        $this->groups[$type]->addOption($option);
    }

    /**
     * @inheritdoc
     */
    public function clearOptions()
    {
        $this->groups = null;
    }

    /**
     * @inheritdoc
     */
    public function getOption($type, $value)
    {
        return isset($this->groups[$type]) ? $this->groups[$type]->getOption($value) : null;
    }

    /**
     * @inheritdoc
     */
    public function getOptionByDisplay($type, $display)
    {
        return isset($this->groups[$type]) ? $this->groups[$type]->getOptionByDisplay($display) : null;
    }

    /**
     * @inheritdoc
     */
    public function getOptions()
    {
        $options = array();
        foreach ($this->groups as $group) {
            array_merge($options, $group->getOptions());
        }
        return $options;
    }

    /**
     * @inheritdoc
     */
    public function setOptions(array $options)
    {
        $this->groups = null;
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
        if (isset($this->groups[$type])) {
            $this->groups[$type]->removeOption($option);
            if ($removeGroup && !sizeof($this->groups[$type]->getOptions())) {
                unset($this->groups[$type]);
            }
        }
    }

    /**
     * @inheritdoc
     */
    public function getOptionGroups()
    {
        return $this->groups;
    }

    /**
     * @inheritdoc
     */
    public function getOptionGroup($name)
    {
        return isset($this->groups[$name]) ? $this->groups[$name] : null;
    }

    /**
     * @inheritdoc
     */
    public function setOptionGroups(array $groups)
    {
        $this->groups = $groups;
    }

    /**
     * @inheritdoc
     */
    public function addOptionGroup(OptionGroupInterface $group)
    {
        $type = $group->getName();
        $this->group[$type] = $group;
    }

    /**
     * @inheritdoc
     */
    public function removeOptionGroup($group)
    {
        if ($group instanceof OptionGroupInterface) {
            $group = $group->getName();
        }
        unset($this->groups[$group]);
    }

    protected function createOptionGroup()
    {
        return new $this->optionGroupClass;
    }
}
