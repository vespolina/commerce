<?php
/**
 * (c) Vespolina Project http://www.vespolina-project.org
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */
namespace Vespolina\ProductBundle\Model\Node;

use Vespolina\ProductBundle\Model\ProductNode;

/**
 * @author Richard D Shank <develop@zestic.com>
 */
class FeatureNode extends ProductNode implements FeatureNodeInterface
{
    protected $term;

    /**
     * Set the search term for this feature
     *
     * @param $term
     */
    public function setSearchTerm($term)
    {
        $this->term = $term;
    }

    /**
     * Return the search term for this feature
     *
     * @return string term
     */
    public function getSearchTerm()
    {
        return $this->term;
    }
}
