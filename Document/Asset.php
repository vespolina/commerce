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

use Vespolina\ProductBundle\Model\Asset as AbstractAsset;
/**
 * @author Myke Hines <myke@webhines.com>
 */
class Asset extends AbstractAsset implements AssetInterface
{
    protected $id;

}
