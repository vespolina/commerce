<?php
/**
 * (c) Vespolina Project http://www.vespolina-project.org
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Vespolina\ProductBundle\Model;

/**
 * @author Richard D Shank <develop@zestic.com>
 */
interface ProductNodeInterface
{
    /**
     * Add a child node to this node
     *
     * @param Vespolina\ProductBundle\Model\ProductNodeInterface $node
     */
    public function addChild(ProductNodeInterface $node);

    /**
     * Get a specific child node
     *
     * @param $name name of the node
     * @return Vespolina\ProductBundle\Model\ProductNodeInterface or null
     */
    public function getChild($name);

    /**
     * Get all of the children nodes
     *
     * @return array of children
     */
    public function getChildren();

    /**
     * Get the parent node, if this node is a child node
     * 
     * @return Vespolina\ProductBundle\Model\ProductNodeInterface or null
     */
    public function getParent();

    /**
     * Test to see if this node is at root level or not
     *
     * @return bool
     */
    public function isRoot();

    /**
     * Set the name of this node
     *
     * @param $name
     */
    public function setName($name);

    /**
     * Get the name of this node
     *
     * @return string name of node
     */
    public function getName();
}