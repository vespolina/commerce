<?php
/**
 * (c) Vespolina Project http://www.vespolina-project.org
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Vespolina\ProductBundle\Model;

use Vespolina\ProductBundle\Model\ProductNodeInterface;

/**
 * @author Richard D Shank <develop@zestic.com>
 */
abstract class ProductNode implements ProductNodeInterface
{
    protected $children;
    protected $name;
    protected $parent;

    /**
     * @inheritdoc
     */
    public function addChild(ProductNodeInterface $node)
    {
        $key = $node->getName();
        $this->children[$key] = $node;

        // set this as the parent in the child
        $rm = new \ReflectionProperty('Vespolina\ProductBundle\Model\ProductNode', 'parent');
        $rm->setAccessible(true);
        $rm->setValue($node, $this);
    }

    /**
     * @inheritdoc
     */
    public function getChild($name)
    {
        if (isset($this->children[$name])) {
            return $this->children[$name];
        }
        return null;
    }

    /**
     * @inheritdoc
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * @inheritdoc
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * @inheritdoc
     */
    public function isRoot()
    {
        return $this->parent ? false : true;
    }

    /**
     * @inheritdoc
     */
    public function setName($name)
    {
        $this->name = (string) $name;
    }

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return $this->name;
    }
}