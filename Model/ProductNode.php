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