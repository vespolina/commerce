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
use Vespolina\ProductBundle\Model\Option\OptionGroupInterface;

/**
 * @author Richard D Shank <develop@zestic.com>
 * @author Daniel Kucharski <daniel@xerias.be>
 */
interface ProductInterface
{
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
     * Add an option set to the product
     *
     * @param Vespolina\ProductBundle\Option\OptionGroupInterface $optionGroup
     *
     */
    public function addOptionGroup(OptionGroupInterface $optionGroup);

    /**
     * Remove an options set from the product
     *
     * @param string name of group $name
     */
    public function removeOptionGroup($name);

    /**
     * Set the options of the product to an option set
     *
     * @param array of Vespolina\ProductBundle\Node\OptionGroupInterface $optionGroup
     */
    public function setOptions($options);

    /**
     * Remove the option groups from the project
     *
     */
    public function clearOptions();

    /**
     * Return the options of the product
     *
     * @return array of Vespolina\ProductBundle\Option\OptionGroupInterface
     */
    public function getOptions();

    /**
     * Return a new instance of the ProductIdentiferSet, based on the class passed into the Product from the constructor
     *
     * @return instance of Vespolina\ProductBundle\Identifier\ProductIdentifierSetInterface
     */
    public function createProductIdentifierSet();

    /**
     * Return the identifier set generated from the option choices
     *
     * @return array of Vespolina\ProductBundle\Identifier\ProductIdentifierSetInterface
     */
    public function getIdentifierSets();

    /**
     * Return an identifier set for the option set combination
     *
     * @param array (optional) option set or null returns primary
     *
     * @return Vespolina\ProductBundle\Identifier\ProductIdentifierSetInterface
     */
    public function getIdentifierSet($target = null);

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
