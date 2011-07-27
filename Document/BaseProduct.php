<?php
/**
 * (c) Vespolina Project http://www.vespolina-project.org
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */
namespace Vespolina\ProductBundle\Document;

use Vespolina\ProductBundle\Model\Product as AbstractProduct;
/**
 * @author Richard Shank <develop@zestic.com>
 */
abstract class BaseProduct extends AbstractProduct
{
    protected $id;
}
