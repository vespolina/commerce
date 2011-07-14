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

/**
 * @author Richard D Shank <develop@zestic.com>
 */
class ProductTest extends WebTestCase
{
    public function testProduct()
    {
        $product = new Product();
        $this->assertInstanceOf(
            'Vespolina\ProductBundle\Model\Node\ProductFeaturesInterface',
            $product->getFeatures(),
            'an empty class with ProductFeaturesInterface should be set');
        $this->assertInstanceOf(
            'Vespolina\ProductBundle\Model\Node\ProductIdentifiersInterface',
            $product->getIdentifiers(),
            'an empty class with ProductIdentifiersInterface should be set');
        $this->assertInstanceOf(
            'Vespolina\ProductBundle\Model\Node\ProductOptionsInterface',
            $product->getOptions(),
            'an empty class with ProductOptionsInterface should be set');
        
    }
}
