<?php
/**
 * (c) Vespolina Project http://www.vespolina-project.org
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */
namespace Vespolina\ProductBundle\Model\Option;

use Vespolina\ProductBundle\Model\ProductNodeInterface;

/**
 * @author Richard D Shank <develop@zestic.com>
 */
interface OptionNodeInterface extends ProductNodeInterface
{
    /**
     * Set the value that is displayed for this option. ie, red, large
     *
     * @param $value
     */
    public function setValue($value);

    /**
     * Return the option value
     *
     * @return string
     */
    public function getValue();

    /**
     * Set the type of option. ie color, size
     *
     * @param $type
     */
    public function setType($type);

    /**
     * Return the type of option
     *
     * @return string
     */
    public function getType();
}
