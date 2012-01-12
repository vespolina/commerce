<?php
/**
 * (c) 2012 Vespolina Project http://www.vespolina-project.org
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */
namespace Vespolina\ProductBundle\Tests\Document;

use Vespolina\ProductBundle\Tests\Fixtures\Document\Product;
use Vespolina\ProductBundle\Document\Feature;
use Vespolina\ProductBundle\Tests\Document\ProductTestCommon;
/**
 * @author Richard D Shank <develop@zestic.com>
 */
class ProductTest extends ProductTestCommon
{
    public function testPersistFeatures()
    {

        $product = $this->productMgr->createProduct();

        $labelFeature = new Feature();
        $labelFeature->setType('label');
        $labelFeature->setName('Joat Music');
        $product->addFeature($labelFeature);

        $formatFeature = new Feature();
        $formatFeature->setType('format');
        $formatFeature->setName('vinyl');
        $product->addFeature($formatFeature);

        $features = $product->getFeatures();
        $this->productMgr->updateProduct($product);

        $persistedProduct = $this->productMgr->findProductById($product->getId());
        $persistedFeatures = $product->getFeatures();

        $this->assertArrayHasKey(0, $persistedFeatures);
        $this->assertArrayHasKey(1, $persistedFeatures);
        $this->assertSame($features->count(), $persistedFeatures->count());

        foreach ($features as $feature) {
            $type = $feature->getType();
            $persistedFeature = $persistedProduct->getFeature($type);
            $this->assertInstanceOf('Vespolina\ProductBundle\Model\Feature\FeatureInterface', $persistedFeature);
            $this->assertSame($feature->getName(), $persistedFeature->getName());
            $this->assertSame($feature->getSearchTerm(), $persistedFeature->getSearchTerm());
        }
    }
}
