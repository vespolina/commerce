<?php
/**
 * (c) Vespolina Project http://www.vespolina-project.org
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */
namespace Vespolina\ProductBundle\Model\Feature;

use Vespolina\ProductBundle\Model\ProductNodeInterface;

/**
 * @author Richard D Shank <develop@zestic.com>
 */
interface FeatureInterface extends ProductNodeInterface
{
    /**
     * Set the search term for this feature
     *
     * @param $term
     */
    public function setSearchTerm($term);

    /**
     * Return the search term for this feature
     *
     * @return string term
     */
    public function getSearchTerm();

    /**
     * Set the type of feature of this node. ie: name, title, brand
     *
     * @param $type
     */
    public function setType($type);

    /**
     * Return the type of feature of this node
     *
     * @return string type
     */
    public function getType();
}
