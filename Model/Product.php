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
use Vespolina\ProductBundle\Model\Node\IdentifierNodeInterface;
use Vespolina\ProductBundle\Model\Node\OptionNodeInterface;
use Vespolina\ProductBundle\Model\Node\ProductIdentifierSet;
use Vespolina\ProductBundle\Model\Node\ProductIdentifierSetInterface;
use Vespolina\ProductBundle\Model\Node\ProductOptions;
use Vespolina\ProductBundle\Model\Node\ProductOptionsInterface;

/**
 * @author Richard D Shank <develop@zestic.com>
 * @author Daniel Kucharski <daniel@xerias.be>
 */
class Product implements ProductInterface
{
    const PHYSICAL      = 1;
    const UNIQUE        = 2;
    const DOWNLOAD      = 4;
    const TIME          = 8;
    const SERVICE       = 16;

    protected $createdAt;
    protected $description;
    protected $features;
    protected $id;
    protected $identifiers;
    protected $name;
    protected $options;
    protected $type;
    protected $updateAt;

    public function __construct()
    {
        $this->options = new ProductOptions();
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->id;
    }

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
    public function addIdentifierSet($key, ProductIdentifierSetInterface $identifier)
    {
        if (!$this->identifiers) {
            $this->identifiers = new ArrayCollection();
        }
        $this->identifiers->set($key, $identifier);
    }

    /**
     * @inheritdoc
     */
    public function setIdentifiers($identifiers)
    {
        $this->identifiers = $identifiers;
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
        return $this->$this->identifiers->get($key);
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
    public function setOptions(ProductOptionsInterface $options)
    {
        $this->options = $options;
    }

    /**
     * @inheritdoc
     */
    public function addOption(OptionNodeInterface $option)
    {
        $this->options->addOption($option);
    }

    /**
     * @inheritdoc
     */
    public function getOptions()
    {
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
