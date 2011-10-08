<?php
/**
 * (c) Vespolina Project http://www.vespolina-project.org
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */
namespace Vespolina\ProductBundle\Model\Node;

use Vespolina\ProductBundle\Model\ProductNodeInterface;
use Vespolina\ProductBundle\Model\Node\ProductIdentifierSetInterface;

/**
 * @author Richard D Shank <develop@zestic.com>
 */
interface ProductIdentifierSetInterface extends ProductNodeInterface
{
    /**
     * A convenience method to add an option to the option set
     * 
     * @param OptionInterface $option
     */
    public function addOption(OptionInterface $option);

    /**
     * A convenience method to remove an option to the option set
     *
     * @param OptionInterface $option
     */
    public function removeOption(OptionInterface $option);

    /**
     * Set options when there are different identifiers with different option sets
     *
     * @param OptionSetInterface $options
     */
    public function setOptions(OptionSetInterface $options);

    /**
     * Return the options for this identifier
     *
     * @return Doctrine\Common\Collections\ArrayCollection $options
     */
    public function getOptions();

    /**
     * Return the OptionSet for this identifier
     *
     * @return OptionSetInterface $options
     */
    public function getOptionSet();

    /**
     * Remove the options for this identifier
     */
    public function removeOptions();

    /**
     * Add a identifier to this product identifiers node.
     *
     * @param Vespolina\ProductBundle\Model\Node\IdentifierNodeInterface $identifier
     */
    public function addIdentifier(IdentifierNodeInterface $identifier);

    /**
     * Clear all identifiers from this product identifiers
     */
    public function clearIdentifiers();

    /**
     * Return a collection of product identifiers
     *
     * @param array identifiers
     */
    public function getIdentifiers();

    /**
     * Add a collection of identifiers
     *
     * @param array $identifiers
     */
    public function setIdentifiers($identifiers);

    /**
     * Remove a identifier from this product identifiers set
     *
     * @param IdentifierNodeInterface $identifier
     */
    public function removeIdentifier(IdentifierNodeInterface $identifier);
}
