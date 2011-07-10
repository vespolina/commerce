<?php
/**
 * (c) Vespolina Project http://www.vespolina-project.org
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */
namespace Vespolina\ProductBundle\Model\Node;

use Vespolina\ProductBundle\Model\ProductNode;
use Vespolina\ProductBundle\Model\Node\FeatureNodeInterface;
use Vespolina\ProductBundle\Model\Node\ProductFeaturesInterface;

/**
 * @author Richard D Shank <develop@zestic.com>
 */
class ProductFeatures extends ProductNode implements ProductFeaturesInterface
{
    /*
     * @inheritdoc
     */
    public function addFeature(FeatureNodeInterface $feature)
    {
        $this->addChild($feature);
    }

    /**
     * @inheritdoc
     */
    public function clearFeatures()
    {
        $this->clearChildren();
    }

    /**
     * @inheritdoc
     */
    public function setFeature($features)
    {
        $this->setChildren($features);
    }

    /**
     * @inheritdoc
     */
    public function removeFeature(FeatureNodeInterface $feature)
    {
        $this->removeChild($feature->getName());
    }
}
