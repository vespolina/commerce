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
class ProductNode implements ProductNodeInterface
{

    protected $productReferences;
    protected $nodes;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->productReferences = array();

    }
 

   
}