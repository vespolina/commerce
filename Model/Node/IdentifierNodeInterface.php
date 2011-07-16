<?php
/**
 * (c) Vespolina Project http://www.vespolina-project.org
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */
namespace Vespolina\ProductBundle\Model\Node;

use Vespolina\ProductBundle\Model\ProductNodeInterface;

/**
 * @author Richard D Shank <develop@zestic.com>
 */
interface IdentifierNodeInterface extends ProductNodeInterface
{
    /**
     * Set the code for this identifier
     *
     * @param $code
     */
    public function setCode($code);

    /**
     * Return the code for this identifier
     *
     * @return code
     */
    public function getCode();
}
