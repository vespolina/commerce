<?php
/**
 * (c) 2011-2012 Vespolina Project http://www.vespolina-project.org
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Vespolina\CartBundle\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Vespolina\Entity\Cart as CoreCart;
/**
 * Cart implements a basic cart implementation
 *
 * @author Daniel Kucharski <daniel@xerias.be>
 * @author Richard Shank <develop@zestic.com>
 */
class Cart extends CoreCart
{
    protected $createdAt;

    /**
     * Constructor
     */
    public function __construct($name)
    {
        $this->items = new ArrayCollection(); // TODO: see if this is really necessary for persistence
        $this->name = $name; // TODO: does this need to be set on initiation?
    }

    /**
     * @inheritdoc
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @inheritdoc
     */
    public function getRecurringItems()
    {
        $recurringItems = array();
        foreach ($this->getItems() as $item) {
            if ($item->isRecurring()) {
                $recurringItems[] = $item;
            }
        }

        return $recurringItems;
    }

    /**
     * @inheritdoc
     */
    public function getNonRecurringItems()
    {
        $nonRecurringItems = array();
        foreach ($this->getItems() as $item) {
            if (!$item->isRecurring()) {
                $nonRecurringItems[] = $item;
            }
        }

        return $nonRecurringItems;
    }

    /**
     * @inheritdoc
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * @inheritdoc
     */
    public function incrementCreatedAt()
    {
        if (null === $this->createdAt) {
            $this->createdAt = new \DateTime();
        }
        $this->updatedAt = new \DateTime();
    }

    /**
     * @inheritdoc
     */
    public function incrementUpdatedAt()
    {
        $this->updatedAt = new \DateTime();
    }
}
