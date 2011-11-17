<?php
/**
 * (c) Vespolina Project http://www.vespolina-project.org
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Vespolina\ProductBundle\Model;

use Vespolina\ProductBundle\Model\Feature\FeatureInterface;
use Vespolina\ProductBundle\Model\Identifier\IdentifierInterface;
use Vespolina\ProductBundle\Model\Identifier\ProductIdentifierSetInterface;
use Vespolina\ProductBundle\Model\Option\OptionInterface;
use Vespolina\ProductBundle\Model\Option\OptionSetInterface;

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
     * @param FeatureInterface $feature
     */
    public function addFeature(FeatureInterface $feature);

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
     * Return the primary ProductIdentifierSet of the product
     *
     * @return identifiers
     */
    public function getPrimaryIdentifierSet();

    /**
     * Set the primary ProductIdentifierSet for this product
     *
     * @param ProductIdentifierSet $primaryIdentifierSet
     */
    public function setPrimaryIdentifierSet($primaryIdentifierSet);

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
    public function setOptions(array $options);

    /**
     * Add options to the product
     */
    public function addOptions(array $option);

    /**
     * Add an option set to the product
     *
     * @param Vespolina\ProductBundle\Option\OptionSetInterface $optionSet
     *
     */
    public function addOptionSet(OptionSetInterface $optionSet);

    /**
     * Remove an options set from the product
     *
     * @param Vespolina\ProductBundle\Option\OptionSetInterface $optionSet
     */
    public function removeOptionSet(OptionSetInterface $optionSet);

    /**
     * Return the options of the product
     *
     * @return array of Vespolina\ProductBundle\Option\OptionSetInterface
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
