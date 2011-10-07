<?php
/**
 * (c) Vespolina Project http://www.vespolina-project.org
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */
namespace Vespolina\ProductBundle\Model\Identifier;

use Vespolina\ProductBundle\Model\Identifier\GS1Identifier;

/**
 * @author Richard D Shank <develop@zestic.com>
 */
class UPCIdentifier extends GS1Identifier
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

    public function getName()
    {
        return 'UPC';
    }
}
