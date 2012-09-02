<?php
/**
 * (c) Vespolina Project http://www.vespolina-project.org
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */
namespace Vespolina\ProductBundle\Model\Identifier;

use Doctrine\Common\Collections\ArrayCollection;

use Vespolina\ProductBundle\Model\Identifier\IdentifierInterface;
use Vespolina\ProductBundle\Model\Identifier\ProductIdentifierSetInterface;

/**
 * @author Richard D Shank <develop@zestic.com>
 */
abstract class ProductIdentifierSet implements ProductIdentifierSetInterface
{
    protected $active;
    protected $id;
    protected $identifiers;
    protected $options;
    protected $upcharge;

    public function __construct($options = null)
    {
        $this->active = true;
        $this->options = $options;
        $this->identifiers = new ArrayCollection();
    }

    public function getId()
    {
        if ($this->id === null) {
            // todo generate id
        }

        return $this->id;
    }

    /*
     * @inheritdoc
     */
    public function addIdentifier(IdentifierInterface $identifier)
    {
        $key = strtolower($identifier->getName());
        $this->identifiers->set($key, $identifier);
    }

    /*
     * @inheritdoc
     */
    public function addIdentifiers(array $identifiers)
    {
        $this->identifiers = array_merge($this->identifiers, $identifiers);
    }

    /**
     * @inheritdoc
     */
    public function clearIdentifiers()
    {
        $this->identifiers = new Arraycollection();
    }

    /**
     * @inheritdoc
     */
    public function getIdentifiers()
    {
        return $this->identifiers;
    }

    /**
     * @inheritdoc
     */
    public function getIdentifier($key)
    {
        $key = strtolower($key);
        return $this->identifiers->get($key);
    }

    /**
     * @inheritdoc
     */
    public function setIdentifiers(array $identifiers)
    {
        $this->identifiers = new ArrayCollection();
        foreach ($identifiers as $identifier) {
            $this->addIdentifier($identifier);
        }
    }

    /**
     * @inheritdoc
     */
    public function removeIdentifier(IdentifierInterface $identifier)
    {
        $key = strtolower($identifier->getName());

        $this->identifiers->remove($key);
    }

    public function getIdentifierTypes()
    {
        return $this->identifiers->getKeys();
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
    public function isActive($set = null)
    {
        if ($set !== null) {
            $this->active = (boolean)$set;
        }

        return $this->active;
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

    public function __get($name)
    {
        if ($key = $this->getIdentifierKey($name)) {
            return $this->getIdentifier($key);
        }
    }

    public function __set($name, $value)
    {
        if ($this->getIdentifierKey($name)) {
            return $this->addIdentifier($value);
        }
    }

    public function __toString()
    {
        return 'ProductIdentifierSet';
    }

    protected function getIdentifierKey($name)
    {
        // get rid of Identifier or _identifier
        if ($position = strpos($name, 'Identifier') . strpos($name, '_identifier') ) {
            return strtolower(substr($name, 0, $position));
        }
        return null;
    }
}
