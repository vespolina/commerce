<?php
/**
 * (c) Vespolina Project http://www.vespolina-project.org
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */
namespace Vespolina\ProductBundle\Model;

use Vespolina\ProductBundle\Model\ProductInterface;

/**
 * @author Richard Shank <develop@zestic.com>
 */
interface ProductManagerInterface
{
    public function createProduct();
    public function findProductById($id);
    public function findProductByIdentifier($name, $code);
}
