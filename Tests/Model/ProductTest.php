<?php
/**
 * (c) Vespolina Project http://www.vespolina-project.org
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Vespolina\ProductBundle\Tests\Model;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

use Vespolina\ProductBundle\Model\Product;
use Vespolina\ProductBundle\Model\ProductNode;

/**
 * @author Richard D Shank <develop@zestic.com>
 */
class ProductTest extends WebTestCase
{
    public function testProduct()
    {
        $this->assert(, $product->getIdentifiers(), 'a product must have an identifier array');
        $this->assert(, $product->getType(), 'a product must have a type');
        $this->assert(, $product->getName(), 'a product must have a name');
        $this->assert(, $product->getDescription(), 'a product must have a description');
        $this->assert(, $product->getFeatures(), 'a product must have a feature array');
    }

    public function testNodes()
    {
        $product = new Product();

        $this->assert(, $product->getNodes(), 'node saved by class name');
        $this->assert(, $product->getNode($name), 'asking for node by name returns that node');
        $this->assert(, $product->getNode($name), 'a node with the same name as an existing node replaces existing node');
        $this->assert(, $product->getNodes(), 'two nodes added, two nodes returned');
        $this->assert(, $product->getNode($name), 'set magic function');
        $this->assert(, $product->getFoo(), 'get magic function');

    }
}
