<?php
/**
 * (c) Vespolina Project http://www.vespolina-project.org
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */
namespace Vespolina\ProductBundle\Model\Option;

use Vespolina\ProductBundle\Model\ProductNode;
use Vespolina\ProductBundle\Model\Option\OptionGroupInterface;

/**
 * @author Richard D Shank <develop@zestic.com>
 */
abstract class OptionGroup extends ProductNode implements OptionGroupInterface
{
    /*
     * @inheritdoc
     */
    public function addOption(OptionInterface $option)
    {
        if (!$this->name) {
            $this->name = $option->getType();
        }
        if ($this->name != $option->getType()) {
            throw new \UnexpectedValueException(sprintf('All OptionsNodes in this type must be %s', $this->name));
        }
        $this->addChild($option, $option->getValue());
    }

    /**
     * @inheritdoc
     */
    public function clearOptions()
    {
        $this->clearChildren();
    }

    /**
     * @inheritdoc
     */
    public function getOption($value)
    {
        return $this->getChild($value);
    }

    /**
     * @inheritdoc
     */
    public function getOptionByName($name)
    {
        return $this->getChildByName($name);
    }

    /**
     * @inheritdoc
     */
    public function getOptions()
    {
        return $this->getChildren();
    }

    /**
     * @inheritdoc
     */
    public function setOptions($options)
    {
        $this->setChildren($options);
    }

    /**
     * @inheritdoc
     */
    public function removeOption(OptionInterface $option)
    {
        $this->removeChild($option->getName());
    }
}
