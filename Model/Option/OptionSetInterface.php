<?php
/**
 * (c) Vespolina Project http://www.vespolina-project.org
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */
namespace Vespolina\ProductBundle\Model\Option;

use Vespolina\ProductBundle\Model\Identifier\ProductIdentifierSetInterface;
use Vespolina\ProductBundle\Model\Option\OptionInterface;


/**
 * @author Richard D Shank <develop@zestic.com>
 */
interface OptionSetInterface
{
    /**
     * Set the product identifierset for this option set
     * 
     * @param ProductIdentifierSetInterface $identifierSet
     */
    public function setIdentifierSet(ProductIdentifierSetInterface $identifierSet);

    public function getIdentifierSet();

    /**
     * Add a option to this product options node.
     *
     * @param Vespolina\ProductBundle\Model\Option\OptionInterface $option
     */
    public function addOption(OptionInterface $option);

    /**
     * Clear all options from this product options
     */
    public function clearOptions();

    /**
     * Return an option in this product
     *
     * @param $type
     * @param $value
     *
     * @return Vespolina\ProductBundle\Model\Option\OptionInterface or null
     */
    public function getOption($type, $value);

    /**
     * Return a specific option by the name
     *
     * @param string $type - the group type
     * @param string $display
     *
     * @return Vespolina\ProductBundle\Model\Option\OptionInterface or null
     */
    public function getOptionByDisplay($type, $display);

    /**
     * Add a collection of all options in all groups
     *
     * @param array $options
     */
    public function setOptions(array $options);

    /**
     * Remove a option from this product options set
     *
     * @param OptionInterface $option
     * @param boolean $removeGroup - by default if there are no options left in the group, it is removed from the set
     */
    public function removeOption(OptionInterface $option, $removeGroup);

    /**
     * Return a collection of the OptionGroups in this set
     * 
     * @return array
     */
    public function getOptionGroups();

    /**
     * Return an OptionGroup by name
     * 
     * @param string $name
     *
     * @return OptionGroupInterface
     */
    public function getOptionGroup($name);

    /**
     * Set a collection of OptionGroups in this set
     *
     * @param array $groups
     */
    public function setOptionGroups(array $groups);

    /**
     * Add a single OptionGroup to the collection
     *
     * @param OptionGroupInterface $group
     */
    public function addOptionGroup(OptionGroupInterface $group);

    /**
     * Remove an OptionGroup from the set
     *
     * @param OptionGroupInterface or string $group
     */
    public function removeOptionGroup($group);
}
