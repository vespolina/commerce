<?php
/**
 * (c) Vespolina Project http://www.vespolina-project.org
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Vespolina\ProductBundle\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use Symfony\Component\DependencyInjection\Exception\ParameterNotFoundException;

use Vespolina\ProductBundle\Model\Feature\FeatureInterface;
use Vespolina\ProductBundle\Model\Identifier\IdentifierInterface;
use Vespolina\ProductBundle\Model\Identifier\ProductIdentifierSetInterface;
use Vespolina\ProductBundle\Model\Option\OptionInterface;
use Vespolina\ProductBundle\Model\Option\OptionGroupInterface;

/**
 * @author Richard D Shank <develop@zestic.com>
 * @author Daniel Kucharski <daniel@xerias.be>
 */
abstract class Product implements ProductInterface
{
    const PHYSICAL      = 1;
    const UNIQUE        = 2;
    const DOWNLOAD      = 4;
    const TIME          = 8;
    const SERVICE       = 16;

    protected $createdAt;
    protected $description;
    protected $features;
    protected $identifiers;
    protected $identifierSetClass;
    protected $name;
    protected $options;
    protected $type;
    protected $updateAt;

    public function __construct($identifierSetClass)
    {
        $this->identifierSetClass = $identifierSetClass;
        $this->identifiers = new ArrayCollection();

        $primaryIdentifierSet = $this->createProductIdentifierSet(array('primary' => 'primary'));
        $this->identifiers->set('primary:primary;', $primaryIdentifierSet);
    }

    /**
     * @inheritdoc
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @inheritdoc
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @inheritdoc
     */
    public function addFeature(FeatureInterface $feature)
    {
        $type = strtolower($feature->getType());
        $searchTerm = strtolower($feature->getSearchTerm());
        $this->features[$type][$searchTerm] = $feature;
    }

    /**
     * @inheritdoc
     */
    public function setFeatures($features)
    {
        $this->features = $features;
    }

    /**
     * @inheritdoc
     */
    public function getFeatures()
    {
        return $this->features;
    }

    /**
     * @inheritdoc
     */
    public function createProductIdentifierSet($options)
    {
        return new $this->identifierSetClass($options);
    }

    /**
     * @inheritdoc
     */
    public function getPrimaryIdentifierSet()
    {
        return $this->identifiers->get('primary');
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

    /**
     * @inheritdoc
     */
    public function addOptionGroup(OptionGroupInterface $optionGroup)
    {
        if (!$this->options instanceof Collection) {
            $this->options = new ArrayCollection();
        }
        $this->options->add($optionGroup);
        $this->identifiers = new ArrayCollection();
        $this->processIdentifiers();
    }

    /**
     * @inheritdoc
     */
    public function removeOptionGroup($group)
    {
        if ($group instanceof OptionGroupInterface) {
            $group = $group->getName();
        }
        foreach ($this->options as $key => $option) {
            if ($option->getName() == $group) {
                $this->options->remove($key);
                $this->identifiers = new ArrayCollection();
                $this->processIdentifiers();
                return;
            }
        }
    }

    /**
     * @inheritdoc
     */
    public function setOptions($optionGroups)
    {
        $identifiers = $this->identifiers;
        $this->clearOptions();
        $this->options = new ArrayCollection;
        foreach ($optionGroups as $optionGroup) {
            $this->options->add($optionGroup);
        }
        $this->identifiers = $identifiers;
        $this->processIdentifiers();
    }

    /**
     * @inheritdoc
     */
    public function clearOptions()
    {
       $this->options = new ArrayCollection();
       $this->identifiers = new ArrayCollection();
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
    public function getIdentifierSets()
    {
        return $this->identifiers;
    }

    /**
     * @inheritdoc
     */
    public function getIdentifierSet($target = null)
    {
        $key = $target ? $this->createKeyFromOptions($target) : 'primary:primary;';
        return $this->identifiers->get($key);
    }

    /**
     * @inheritdoc
     */
    public function getOptionSet($target)
    {
        foreach ($this->identifiers as $optionSet) {
            if (!count(array_diff_assoc($optionSet->getOptions(), $target))) {
                return $optionSet;
            }
        }
        return null;
    }

    /**
     * @inheritdoc
     */
    public function addIdentifier($identifier, $target = null)
    {
        $key = $target ? $target : 'primary:primary;';
        if (is_array($key)) {
            $key = $this->createKeyFromOptions($target);
        }
        if (!$idSet = $this->identifiers->get($key)) {
            $optionGroup = key($target);
            throw new ParameterNotFoundException(sprintf('There is not an option group %s with the option %s', $optionGroup, $target[$optionGroup]));
        }
        $idSet->addIdentifier($identifier);

        $this->processIdentifiers();
    }

    /**
     * @inheritdoc
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @inheritdoc
     */
    public function getType()
    {
        return $this->type;
    }

    /*
     * @inheritdoc
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /*
     * @inheritdoc
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /*
     * @inheritdoc
     */
    public function processIdentifiers()
    {
        $optionSet = array();
        foreach ($this->options as $productOption) {
            $options = $productOption->getOptions();
            if ($options) {
                $choices = array();
                $name = $productOption->getName();
                foreach($options as $option) {
                    $choices[] = array($name => $option->getValue());
                }
                $optionSet[$name] = $choices;
            }
        }

        ksort($optionSet);
        if ($optionCombos = $this->extractOptionCombos($optionSet)) {
            foreach ($optionCombos as $key => $combo) {
                if (!$this->identifiers->containsKey($key)) {
                    $this->identifiers->set($key, $this->createProductIdentifierSet($combo));
                }
            }
        }
    }

    protected function extractOptionCombos($optionSet)
    {
        if ($curSet = array_shift($optionSet)) {
            $combos = $this->extractOptionCombos($optionSet);
            $return = array();
            foreach ($curSet as $option) {
                $key = $this->createKeyFromOptions($option);
                if ($combos) {
                    foreach ($combos as $curKey => $curCombo) {
                        $returnKey = $key . $curKey;
                        $return[$returnKey] = array_merge($option, $curCombo);
                    }
                } else {
                    $return[$key] = $option;
                }
            }
            return $return;
        }
        return null;
    }

    public function incrementCreatedAt()
    {
        if (null === $this->createdAt) {
            $this->createdAt = new \DateTime();
        }
        $this->updatedAt = new \DateTime();
    }

    public function incrementUpdatedAt()
    {
        $this->updatedAt = new \DateTime();
    }

    protected function createKeyFromOptions($options)
    {
        $key = '';
        ksort($options);
        foreach ($options as $optionGroup => $option) {
            $key .= sprintf('%s:%s;', $optionGroup, $option);
        }
        return $key;
    }
}
