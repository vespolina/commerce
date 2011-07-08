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