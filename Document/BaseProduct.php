<?php
/**
 * (c) Vespolina Project http://www.vespolina-project.org
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */
namespace Vespolina\ProductBundle\Document;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use Vespolina\ProductBundle\Model\Product as AbstractProduct;
/**
 * @author Richard Shank <develop@zestic.com>
 */
abstract class BaseProduct extends AbstractProduct
{
    protected $id;

    protected $features;
    protected $identifiers;
    protected $identifierSetClass;
    protected $asset_by_type;

    public function __construct($identifierSetClass)
    {
        $this->features = array();

        $this->identifierSetClass = $identifierSetClass;
        $this->identifiers = new ArrayCollection();
    }

    public function findAsset($type)
    {
        // If we have already retrieved the asset, just return it
        if (isset($this->asset_by_type[$type]))
            return $this->asset_by_type[$type];

        foreach ($this->getAssets() as $asset)
        {
            if ($asset->getType() == $type)
            {
                $this->asset_by_type[$type] = $asset;
                return $asset;
            }

        }
        return false;
    }
}
