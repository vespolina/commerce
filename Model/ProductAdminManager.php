<?php
/**
 * (c) Vespolina Project http://www.vespolina-project.org
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */
namespace Vespolina\ProductBundle\Model;

use Vespolina\ProductBundle\Model\ProductAdminManagerInterface;
/**
 * @author Richard Shank <develop@zestic.com>
 */
abstract class ProductAdminManager implements ProductAdminManagerInterface
{
    protected $objectGroupClass;

    public function __construct($objectGroupClass)
    {
        $this->optionGroupClass = $objectGroupClass;
    }

    public function createOptionGroup()
    {
        return new $this->optionGroupClass;
    }
}
