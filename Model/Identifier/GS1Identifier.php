<?php
/**
 * (c) Vespolina Project http://www.vespolina-project.org
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */
namespace Vesoplina\ProductBundle\Model\Identifier;

use Vesoplina\ProductBundle\Model\Node\IdentifierNode;

/**
 * @author Richard D Shank <develop@zestic.com>
 */
abstract class GS1Identifier extends IdentifierNode
{
    /**
     * Performs a redundancy check on the identifier code
     *
     * @return boolean
     */
    public function checkDigit()
    {
        return false;
    }
}
