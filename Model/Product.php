<?php
/**
 * (c) Vespolina Project http://www.vespolina-project.org
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Vespolina\ProductBundle\Model;

use Doctrine\Common\Collections\ArrayCollection;

use Vespolina\ProductBundle\Model\ProductNodeInterface;
use Vespolina\ProductBundle\Model\Node\FeatureNodeInterface;
use Vespolina\ProductBundle\Model\Identifier\IdentifierInterface;
use Vespolina\ProductBundle\Model\Identifier\ProductIdentifierSetInterface;
use Vespolina\ProductBundle\Model\Option\OptionInterface;
use Vespolina\ProductBundle\Model\Option\OptionSet;
use Vespolina\ProductBundle\Model\Option\OptionSetInterface;

/**
 * @author Richard D Shank <develop@zestic.com>
 * @author Daniel Kucharski <daniel@xerias.be>
 */
abstract class Product implements ProductInterface
{
    const PHYSICAL      = 1;
    const UNIQUE        = 2;
    const DOWNLOAD      = 4;
    const TIME          = 8;
    const SERVICE       = 16;

    protected $createdAt;
    protected $description;
    protected $features;
    protected $identifiers;
    protected $name;
    protected $options;
    protected $type;
    protected $updateAt;

    /**
     * @inheritdoc
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @inheritdoc
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @inheritdoc
     */
    public function addFeature(FeatureNodeInterface $feature)
    {
        $type = strtolower($feature->getType());
        $searchTerm = strtolower($feature->getSearchTerm());
        $this->features[$type][$searchTerm] = $feature;
    }

    /**
     * @inheritdoc
     */
    public function setFeatures($features)
    {
        $this->features = $features;
    }

    /**
     * @inheritdoc
     */
    public function getFeatures()
    {
        return $this->features;
    }

    /**
     * @inheritdoc
     */
    public function addIdentifierSet($key, ProductIdentifierSetInterface $identifierSet)
    {
        if (!$this->identifiers) {
            $this->identifiers = new ArrayCollection();
        }
        $this->identifiers->set($key, $identifierSet);
    }

    /**
     * @inheritdoc
     */
    public function clearIdentifiers()
    {
        $this->identifiers = new ArrayCollection();
    }

    /**
     * @inheritdoc
     */
    public function getIdentifiers()
    {
        return $this->identifiers;
    }

    /**
     * @inheritdoc
     */
    public function getIdentifierSet($key)
    {
        return $this->identifiers->get($key);
    }

    /**
     * Remove an identifier set by key from this product
     * 
     * @param $key
     */
    public function removeIdentifierSet($key)
    {
        $this->identifiers->remove($key);
    }

    /**
     * @inheritdoc
     */
    public function setIdentifiers($identifiers)
    {
        if (!$identifiers instanceof ArrayCollection) {
            $this->identifiers = new ArrayCollection($identifiers);
            return;
        }
        $this->identifiers = $identifiers;
    }

    /**
     * @inheritdoc
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @inheritdoc
     */
    public function setOptions(OptionSetInterface $options)
    {
        $this->options = $options;
    }

    /**
     * @inheritdoc
     */
    public function addOption(OptionInterface $option)
    {
        $this->options->addOption($option);
    }

    /**
     * @inheritdoc
     */
    public function getOptions()
    {
        if (!$this->options) {
            // todo: make this work with configuration
            $this->options = new OptionSet(new optionGroupClass());
        }
        return $this->options;
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

    /*
     * @inheritdoc
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /*
     * @inheritdoc
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    public function incrementCreatedAt()
    {
        if (null === $this->createdAt) {
            $this->createdAt = new \DateTime();
        }
        $this->updatedAt = new \DateTime();
    }

    public function incrementUpdatedAt()
    {
        $this->updatedAt = new \DateTime();
    }
}
