<?php
/**
 * (c) Vespolina Project http://www.vespolina-project.org
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */
namespace Vespolina\ProductBundle\Form\Model;

use Vespolina\ProductBundle\Model\Node\IdentifierNodeInterface;
/**
 * @author Richard D Shank <develop@zestic.com>
 */
class CheckProduct
{
    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $description;

    /**
     * @var string
     */
    public $identifier;
}