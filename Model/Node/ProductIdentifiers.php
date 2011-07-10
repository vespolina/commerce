<?php
/**
 * (c) Vespolina Project http://www.vespolina-project.org
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */
namespace Vespolina\ProductBundle\Model\Node;

use Vespolina\ProductBundle\Model\ProductNode;
use Vespolina\ProductBundle\Model\Node\IdentifierNodeInterface;
use Vespolina\ProductBundle\Model\Node\ProductIdentifiersInterface;

/**
 * @author Richard D Shank <develop@zestic.com>
 */
class ProductIdentifiers extends ProductNode implements ProductIdentifiersInterface
{
    /*
     * @inheritdoc
     */
    public function addIdentifier(IdentifierNodeInterface $identifier)
    {
        $this->addChild($identifier);
    }

    /**
     * @inheritdoc
     */
    public function clearIdentifiers()
    {
        $this->clearChildren();
    }

    /**
     * @inheritdoc
     */
    public function setIdentifier($identifiers)
    {
        $this->setChildren($identifiers);
    }

    /**
     * @inheritdoc
     */
    public function removeIdentifier(IdentifierNodeInterface $identifier)
    {
        $this->removeChild($identifier->getName());
    }
}
