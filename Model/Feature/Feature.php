<?php
/**
 * (c) Vespolina Project http://www.vespolina-project.org
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */
namespace Vespolina\ProductBundle\Model\Feature;

use Vespolina\ProductBundle\Model\ProductNode;

/**
 * @author Richard D Shank <develop@zestic.com>
 */
abstract class Feature extends ProductNode implements FeatureInterface
{
    protected $searchTerm;
    protected $type;

    /**
     * @inheritdoc
     */
    public function setSearchTerm($searchTerm)
    {
        $this->searchTerm = strtolower($searchTerm);
    }

    /**
     * @inheritdoc
     */
    public function getSearchTerm()
    {
        return $this->searchTerm;
    }

    /**
     * @inheritdoc
     */
    public function setName($name)
    {
        $this->name = $name;
        if (!$this->searchTerm) {
            $this->setSearchTerm($name);
        }
    }

    /**
     * @inheritdoc
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @inheritdoc
     */
    public function getType()
    {
        return $this->type;
    }
}
