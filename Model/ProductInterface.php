<?php
/**
 * (c) Vespolina Project http://www.vespolina-project.org
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Vespolina\ProductBundle\Model;

use Vespolina\ProductBundle\Model\ProductNodeInterface;
use Vespolina\ProductBundle\Model\Node\FeatureNodeInterface;
use Vespolina\ProductBundle\Model\Node\IdentifierNodeInterface;
use Vespolina\ProductBundle\Model\Node\OptionNodeInterface;
use Vespolina\ProductBundle\Model\Node\ProductFeaturesInterface;
use Vespolina\ProductBundle\Model\Node\ProductIdentifierSetInterface;
use Vespolina\ProductBundle\Model\Node\OptionSetInterface;

/**
 * @author Richard D Shank <develop@zestic.com>
 * @author Daniel Kucharski <daniel@xerias.be>
 */
interface ProductInterface
{
    /**
     * Return the system id for this product
     *
     * @return $id
     */
    public function getId();

    /**
     * Set the description of the product
     *
     * @param $description
     */
    public function setDescription($description);

    /**
     * Return the description of the product
     *
     * @return string
     */
    public function getDescription();

    /**
     * Add a single feature to the product
     *
     * @param ProductNodeInterface $feature
     */
    public function addFeature(FeatureNodeInterface $feature);

    /**
     * Set the features of the product to a feature set
     *
     * @param $features
     */
    public function setFeatures($features);

    /**
     * Return the features of the product
     *
     * @return ProductFeaturesInterface
     */
    public function getFeatures();

    /**
     * Add a ProductIdentifierSet to the ArrayCollection. The key is the primary identifier used in a
     * search to find the ProductIdentifiers
     *
     * @param string $key
     * @param Vespolina\ProductBundle\Node\ProductIdentifierSetInterface $identifier
     */
    public function addIdentifierSet($key, ProductIdentifierSetInterface $identifier);

    /**
     * Return a ProductIdentifiers of the product by the key
     *
     * @param string $key
     *
     * @return Vespolina\ProductBundle\Node\ProductIdentifiersInterface $identifiers
     */
    public function getIdentifierSet($key);

    /**
     * Return the identifiers of the product
     *
     * @return identifiers
     */
    public function getIdentifiers();

    /**
     * Remove an identifier set by key from this product
     *
     * @param $key
     */
    public function removeIdentifierSet($key);

    /**
     * Set a collection of ProductIdentifierSet for this product
     *
     * @param identifiers
     */
    public function setIdentifiers($identifiers);

    /**
     * Set the name of the product
     *
     * @param $name
     */
    public function setName($name);

    /**
     * Return the name of the product
     *
     * @return string
     */
    public function getName();

    /**
     * Set the options of the product to an option set
     *
     * @param Vespolina\ProductBundle\Node\OptionSetInterface $options
     */
    public function setOptions(OptionSetInterface $options);

    /**
     * Add an option to the product
     */
    public function addOption(OptionNodeInterface $option);

    /**
     * Return the options of the product
     *
     * @return Vespolina\ProductBundle\Node\OptionSetInterface $options
     */
    public function getOptions();

    /**
     * Use a different name or different technique
     *
     * These are valid types of products
     * Product::PHYSICAL
     * Product::UNIQUE
     * Product::DOWNLOAD
     * Product::TIME
     * Product::SERVICE
     *
     * @param $type
     */

    /**
     * Set the product type, ie shirt, cd, tickets
     * 
     * @param $type
     */
    public function setType($type);

    /**
     * Get the product type.
     * @return type
     */
    public function getType();

    /*
     * Get the date and time the product was created
     *
     * @return \DateTime
     */
    public function getCreatedAt();

    /*
     * Get the date and time the product was last updated
     *
     * @return \DateTime
     */
    public function getUpdatedAt();
}
