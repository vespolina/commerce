<?php
/**
 * (c) Vespolina Project http://www.vespolina-project.org
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */
namespace Vespolina\ProductBundle\Model\Node;

use Vespolina\ProductBundle\Model\ProductNode;
use Vespolina\ProductBundle\Model\Node\OptionNodeInterface;

/**
 * @author Richard D Shank <develop@zestic.com>
 */
class OptionNode extends ProductNode implements OptionNodeInterface
{
    protected $value;
    protected $type;

    /**
     * Set the value that is displayed for this option. ie, red, large
     *
     * @param $value
     */
    public function setValue($value)
    {
        $this->value = $value;
    }

    /**
     * Return the option value
     *
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Set the type of option. ie color, size
     *
     * @param $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * Return the type of option
     * 
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }
}
