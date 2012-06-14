<?php
/**
 * (c) 2011-2012 Vespolina Project http://www.vespolina-project.org
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */
namespace Vespolina\ProductBundle\Model;

use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

/**
 * @author Myke Hines <myke@webhines.com>
 */
class AssetManager
{
    private $dm;
    protected $assetModelClass;

    /**
     * Searches for a particular asset type from a product
     *
     */
    public function hasType( ProductInterface $product, $type)
    {
        if ($this->findAssetByType($product, $type))
            return true;
        return false;
    }

    /**
     * Returns a single asset (first) of a particular type
     */
    public function getType (ProductInterface $product, $type)
    {
        return $this->findAssetByType($product, $type);
    }

    /**
     * Returns all assets of a particular type
     */
    public function getTypes (ProductInterface $product, $type)
    {
        return $this->findAssetsByType($product, $type);
    }

    /**
     * Creates an asset and links to a product
     * $product must be persisted before this action is executed
     */
    public function createAsset (ProductInterface $product, $fileLocation, $type)
    {
        $baseClass = $this->assetModelClass;
        $asset = new $baseClass;
        $asset->setType($type);
        $asset->setFileName($fileLocation);
        $asset->setProduct($product);

        $this->updateasset($asset, true);
        return $asset;
    }

}
