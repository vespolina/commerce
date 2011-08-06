<?php
/**
 * (c) Vespolina Project http://www.vespolina-project.org
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */
namespace Vespolina\ProductBundle\Document;

use Vespolina\ProductBundle\Document\BaseProduct;
use Vespolina\ProductBundle\Document\OptionSet;
/**
 * @author Richard Shank <develop@zestic.com>
 */
class Product extends BaseProduct
{
    public function __construct()
    {
        parent::__construct(new OptionSet());
    }
}
