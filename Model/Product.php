<?php
/**
 * (c) Vespolina Project http://www.vespolina-project.org
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Vespolina\ProductBundle\Model;

use Vespolina\ProductBundle\Model\ProductNodeInterface;
use Vespolina\ProductBundle\Model\Node\FeatureInterface;
use Vespolina\ProductBundle\Model\Node\IdentifierInterface;
use Vespolina\ProductBundle\Model\Node\OptionInterface;
use Vespolina\ProductBundle\Model\Node\ProductFeatures;
use Vespolina\ProductBundle\Model\Node\ProductFeaturesInterface;
use Vespolina\ProductBundle\Model\Node\ProductIdentifiers;
use Vespolina\ProductBundle\Model\Node\ProductIdentifiersInterface;
use Vespolina\ProductBundle\Model\Node\ProductOptions;
use Vespolina\ProductBundle\Model\Node\ProductOptionsInterface;

/**
 * @author Richard D Shank <develop@zestic.com>
 */
class Product implements ProductInterface
{
    protected $features;
    protected $description;
    protected $id;
    protected $identifiers;
    protected $name;
    protected $options;
    protected $type;

    public function __construct()
    {
        $this->features = new ProductFeatures();
        $this->identifiers = new ProductIdentifiers();
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
    public function addFeature(FeatureInterface $feature)
    {

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
    public function addIdentifier(IdentifierNodeInterface $identifier)
    {

    }
    
    /**
     * @inheritdoc
     */
    public function setIdentifiers(ProductIdentifiersInterface $identifiers)
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
}
