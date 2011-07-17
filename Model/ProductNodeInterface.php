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
 * @author Daniel Kucharski <daniel@xerias.be>
 */
interface ProductNodeInterface
{
    /**
     * Add a child node to this node. By default the associative name of the node is the node name.
     *
     * @param Vespolina\ProductBundle\Model\ProductNodeInterface $node
     * @param optional $name override associate name of the node
     */
    public function addChild(ProductNodeInterface $node, $name = null);

    /**
     * Clear all children from this node
     */
    public function clearChildren();

    /**
     * Remove a child node
     *
     * @param $name name of node
     */
    public function removeChild($name);

    /**
     * Set a collection of children
     *
     * @param array $children
     */
    public function setChildren($children);

    /**
     * Get a specific child node by the associative name
     *
     * @param $name associative name of the node
     * @return Vespolina\ProductBundle\Model\ProductNodeInterface or null
     */
    public function getChild($name);

    /**
     * Get a specific child node
     *
     * @param $name name of the node
     * @return Vespolina\ProductBundle\Model\ProductNodeInterface or null
     */
    public function getChildByName($name);

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