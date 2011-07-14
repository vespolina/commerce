<?php
/**
 * (c) Vespolina Project http://www.vespolina-project.org
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */
namespace Vesoplina\ProductBundle\Model\Node;

use Vespolina\ProductBundle\Model\ProductNode;
use Vesoplina\ProductBundle\Model\Node\IdentifierNodeInterface;

/**
 * @author Richard D Shank <develop@zestic.com>
 */
class IdentifierNode extends ProductNode
{
    protected $code;

    /**
     * Set the code for this identifier
     *
     * @param $code
     */
    public function setCode($code)
    {
        $this->code = $code;
    }

    /**
     * Return the code for this identifier
     *
     * @return code
     */
    public function getCode()
    {
        return $this->code;
    }
}
