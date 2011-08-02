<?php
/**
 * (c) Vespolina Project http://www.vespolina-project.org
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */
namespace Vespolina\ProductBundle\Model\Identifier;

use Vespolina\ProductBundle\Model\Node\IdentifierNode;

/**
 * @author Richard D Shank <develop@zestic.com>
 */
class SKUIdentifier extends IdentifierNode
{

    public function getName()
    {
        return 'SKU';
    }
}
