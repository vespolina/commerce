<?php
/**
 * (c) Vespolina Project http://www.vespolina-project.org
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */
namespace Vespolina\ProductBundle\Model\Option;

use Vespolina\ProductBundle\Model\Option\OptionInterface;

/**
 * @author Richard D Shank <develop@zestic.com>
 */
abstract class Option implements OptionInterface
{
    protected $display;
    protected $type;
    protected $value;
    protected $upcharge;

    /*
     * @inheritdoc
     */
    public function setValue($value)
    {
        $this->value = $value;
    }
    /*
     * @inheritdoc
     */
    public function getValue()
    {
        return $this->value;
    }

    /*
     * @inheritdoc
     */
    public function setDisplay($display)
    {
        $this->display = $display;
    }

    /*
     * @inheritdoc
     */
    public function getDisplay()
    {
        return $this->display;
    }

    /*
     * @inheritdoc
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /*
     * @inheritdoc
     */
    public function getType()
    {
        return $this->type;
    }

    /*
     * @inheritdoc
     */
    public function setUpcharge($upcharge)
    {
        $this->upcharge = $upcharge;
    }

    /*
     * @inheritdoc
     */
    public function getUpcharge()
    {
        return $this->upcharge;
    }

    public function __toString()
    {
        return $this->display;
    }
}
