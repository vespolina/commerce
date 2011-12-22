<?php
/**
 * (c) Vespolina Project http://www.vespolina-project.org
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */
namespace Vespolina\ProductBundle\Model\Identifier;

use Vespolina\ProductBundle\Model\Identifier\IdentifierInterface;
use Vespolina\ProductBundle\Model\Identifier\ProductIdentifierSetInterface;

/**
 * @author Richard D Shank <develop@zestic.com>
 */
abstract class ProductIdentifierSet implements ProductIdentifierSetInterface
{
    protected $identifiers;
    protected $options;

    public function __construct($options)
    {
        $this->options = $options;
    }

    /*
     * @inheritdoc
     */
    public function addIdentifier(IdentifierInterface $identifier)
    {
        $key = strtolower($identifier->getName());
        $this->identifiers[$key] = $identifier;
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
        $this->identifiers = null;
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
        return isset($this->identifiers[$key]) ? $this->identifiers[$key] : null;
    }

    /**
     * @inheritdoc
     */
    public function setIdentifiers(array $identifiers)
    {
        $this->identifiers = $identifiers;
    }

    /**
     * @inheritdoc
     */
    public function removeIdentifier(IdentifierInterface $identifier)
    {
        foreach ($this->identifiers as $key => $curIdentifier) {
            if ($curIdentifier == $identifier) {
                unset($this->identifiers[$key]);
            }
        }
    }

    /**
     * @inheritdoc
     */

    public function getOptions()
    {
        return $this->options;
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
