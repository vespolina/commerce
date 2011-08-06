<?php
/**
 * (c) Vespolina Project http://www.vespolina-project.org
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */
namespace Vespolina\ProductBundle\Model\Node;

use Vespolina\ProductBundle\Model\ProductNode;
use Vespolina\ProductBundle\Model\Node\OptionTypeNode;
use Vespolina\ProductBundle\Model\Node\OptionSetInterface;

/**
 * @author Richard D Shank <develop@zestic.com>
 */
class OptionSet extends ProductNode implements OptionSetInterface
{
    /*
     * @inheritdoc
     */
    public function addOption(OptionNodeInterface $option)
    {
        $typeName = $option->getType();
        if (!isset($this->children[$typeName])) {
            $this->children[$typeName] = new OptionTypeNode();
        }
        $this->children[$typeName]->addOption($option);
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
    public function getOption($type, $value)
    {
        return isset($this->children[$type]) ? $this->children[$type]->getOption($value) : null;
    }

    /**
     * @inheritdoc
     */
    public function getOptionByName($name)
    {
        foreach ($this->children as $child) {
            if ($option = $child->getOptionByName($name)) {
                return $option;
            }
        }
        return null;
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
    public function removeOption(OptionNodeInterface $option)
    {
        $this->removeChild($option->getName());
    }

    /**
     * @inheritdoc
     */
    public function getType($type)
    {
        return $this->getChild($type);
    }
}
