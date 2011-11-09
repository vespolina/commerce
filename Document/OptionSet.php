<?php
/**
 * (c) Vespolina Project http://www.vespolina-project.org
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */
namespace Vespolina\ProductBundle\Document;

use Vespolina\ProductBundle\Model\Option\OptionSet as AbstractOptionSet;
/**
 * @author Richard D Shank <develop@zestic.com>
 */
class OptionSet extends AbstractOptionSet
{
    public function __construct($optionGroupClass)
    {
        parent::__construct($optionGroupClass);
    }
}
