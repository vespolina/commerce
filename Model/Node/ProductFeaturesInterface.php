<?php
/**
 * (c) Vespolina Project http://www.vespolina-project.org
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */
namespace Vespolina\ProductBundle\Model\Node;

use Vespolina\ProductBundle\Model\ProductNodeInterface;
use Vespolina\ProductBundle\Model\Node\FeatureNodeInterface;

/**
 * @author Richard D Shank <develop@zestic.com>
 */
interface ProductFeaturesInterface extends ProductNodeInterface
{
    /**
     * Add a feature to this product features node.
     *
     * @param Vespolina\ProductBundle\Model\Node\FeatureNodeInterface $feature
     */
    public function addFeature(FeatureNodeInterface $feature);

    /**
     * Clear all features from this product features
     */
    public function clearFeatures();

    /**
     * Add a collection of features
     * 
     * @param array $features
     */
    public function setFeature($features);

    /**
     * Remove a feature from this product features set
     *
     * @param FeatureNodeInterface $feature
     */
    public function removeFeature(FeatureNodeInterface $feature);
}
