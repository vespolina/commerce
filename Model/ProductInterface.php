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
use Vespolina\ProductBundle\Model\Node\ProductIdentifiersInterface;
use Vespolina\ProductBundle\Model\Node\ProductOptionsInterface;

/**
 * @author Richard D Shank <develop@zestic.com>
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
     * Set the identifiers of the product to an identifiers set
     *
     * @param Vespolina\ProductBundle\Node\ProductIdentifiersInterface $identifiers
     */
    public function setIdentifiers(ProductIdentifiersInterface $identifiers);

    /**
     * Add an identifier to the product
     *
     * @param IdentifierNodeInterface $identifier
     */
    public function addIdentifier(IdentifierNodeInterface $identifier);

    /**
     * Return the identifiers of the product
     *
     * @return Vespolina\ProductBundle\Node\ProductIdentifiersInterface $identifiers
     */
    public function getIdentifiers();

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
     * @param Vespolina\ProductBundle\Node\ProductOptionsInterface $options
     */
    public function setOptions(ProductOptionsInterface $options);

    /**
     * Add an option to the product
     */
    public function addOption(OptionNodeInterface $option);

    /**
     * Return the options of the product
     *
     * @return Vespolina\ProductBundle\Node\ProductOptionsInterface $options
     */
    public function getOptions();

    /**
     * Set the primary identifier class by name or instance
     *
     * @param string or instance of Vespolina\ProductBundle\Node\IdentifierNodeInterface
     */
    public function setPrimaryIdentifier($primaryIdentifier);

    /**
     * Return the name of the primary identifier node class
     *
     * @return string
     */
    public function getPrimaryIdentifier();

    /**
     * Set the product type. The product types can be ORed together
     *
     * These are valid types of products
     * Product::PHYSICAL
     * Product::UNIQUE
     * Product::DOWNLOAD
     *
     * @param $type
     */
    public function setType($type);

    /**
     * Get the product type.
     * @return type
     */
    public function getType();

#    public function addNode(ProductNodeInterface $node);
#    public function setNodes($nodes);
#    public function getNode($name);
#    public function getNodes();
}
