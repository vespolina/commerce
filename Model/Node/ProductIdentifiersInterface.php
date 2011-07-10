<?php
/**
 * (c) Vespolina Project http://www.vespolina-project.org
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */
namespace Vespolina\ProductBundle\Model\Node;

use Vespolina\ProductBundle\Model\ProductNodeInterface;
use Vespolina\ProductBundle\Model\Node\ProductIdentifiersInterface;

/**
 * @author Richard D Shank <develop@zestic.com>
 */
interface ProductIdentifiersInterface extends ProductNodeInterface
{
    /**
     * Add a identifier to this product identifiers node.
     *
     * @param Vespolina\ProductBundle\Model\Node\IdentifierNodeInterface $identifier
     */
    public function addIdentifier(IdentifierNodeInterface $identifier);

    /**
     * Clear all identifiers from this product identifiers
     */
    public function clearIdentifiers();

    /**
     * Add a collection of identifiers
     * 
     * @param array $identifiers
     */
    public function setIdentifier($identifiers);

    /**
     * Remove a identifier from this product identifiers set
     *
     * @param IdentifierNodeInterface $identifier
     */
    public function removeIdentifier(IdentifierNodeInterface $identifier);
}
