<?php
/**
 * (c) 2012 Vespolina Project http://www.vespolina-project.org
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Vespolina\ProductBundle\Tests\Document;

use Vespolina\ProductBundle\Document\ProductManager;
use Vespolina\ProductBundle\Tests\Document\ProductTestCommon;

/**
 * @author Richard D Shank <develop@zestic.com>
 */
class ProductManagerTest extends ProductTestCommon
{
    public function testFindByName()
    {
        $product = $this->persistNewProduct('test');

        $this->assertInstanceOf('Vespolina\Entity\ProductInterface', $this->productMgr->findProductByName('test'), 'a single result should return that product');

        $this->persistNewProduct('test');
        $this->assertInstanceOf('Doctrine\ODM\MongoDB\Cursor', $this->productMgr->findProductByName('test'), 'multiple results returns collection');
    }

    public function setup()
    {
        parent::setup();
    }

    public function tearDown()
    {
        parent::tearDown();
    }
}
