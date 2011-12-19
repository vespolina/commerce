<?php
/**
 * (c) Vespolina Project http://www.vespolina-project.org
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */
namespace Vespolina\ProductBundle\Model\Option;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use Vespolina\ProductBundle\Model\Option\OptionGroupInterface;

/**
 * @author Richard D Shank <develop@zestic.com>
 */
abstract class OptionGroup implements OptionGroupInterface
{
    protected $options;
    protected $name;
    protected $display;
    protected $required;

    /*
     * @inheritdoc
     */
    public function addOption(OptionInterface $option)
    {
        if (!$this->options instanceof Collection) {
            $this->options = new ArrayCollection();
        }
        $optionType = $option->getType();
        if (!$this->name && !$optionType) {
            throw new \UnexpectedValueException('The OptionGroup must have the name set or the Option must have the group type set');
        }
        if (!$optionType) {
            $option->setType($this->name);
        }
        if (!$this->name) {
            $this->name = $optionType;
        }
        if ($this->name != $option->getType()) {
            throw new \UnexpectedValueException(sprintf('All OptionsNodes in this type must be %s', $this->name));
        }
        $this->options->add($option);
    }

    /**
     * @inheritdoc
     */
    public function clearOptions()
    {
        $this->options = new ArrayCollection();
    }

    /**
     * @inheritdoc
     */
    public function getOption($value)
    {
        return isset($this->options[$value]) ? $this->options[$value] : null;
    }

    /**
     * @inheritdoc
     */
    public function getOptionByDisplay($display)
    {
        foreach ($this->options as $option) {
            if ($display == $option->getDisplay()) {
                return $option;
            }
        }
        return null;
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
    public function setOptions($options)
    {
        $this->clearOptions();
        foreach ($options as $option) {
            $this->addOption($option);
        }
    }

    /**
     * @inheritdoc
     */
    public function removeOption(OptionInterface $option)
    {
        $this->option->remove($option);
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

    public function setDisplay($display)
    {
        $this->display = $display;
    }

    public function getDisplay()
    {
        return $this->display;
    }

    public function setRequired($required)
    {
        $this->required = $required;
    }

    public function getRequired()
    {
        return $this->required;
    }
}
