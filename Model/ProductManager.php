<?php
/**
 * (c) Vespolina Project http://www.vespolina-project.org
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */
namespace Vespolina\ProductBundle\Model;

use Vespolina\ProductBundle\Model\ProductInterface;
use Vespolina\ProductBundle\Model\ProductManagerInterface;
use Vespolina\ProductBundle\Model\Node\ProductIdentifierSetInterface;

/**
 * @author Richard Shank <develop@zestic.com>
 */
abstract class ProductManager implements ProductManagerInterface
{
    /**
     * @inheritdoc
     */
    public function getPrimaryIdentifier()
    {
        if (!$primaryIdentifier = $this->container->getParameter('vespolina_product.primary_identifier.class')) {
            throw new \UnexpectedValueException('The primary identifier type has not been set');
        }
        return $primaryIdentifier;
    }

    /**
     * @inheritdoc
     */
    public function addIdentifierSetToProduct(ProductIdentifierSetInterface $identifierSet, ProductInterface &$product)
    {
        $index = $this->getIdentifierSetIndex($identifierSet);
        $product->addIdentifierSet($index, $identifierSet);
    }

    /**
     * @inheritdoc
     */
    public function removeIdentifierSetFromProduct($identifierSet, ProductInterface &$product)
    {
        if (!is_string($identifierSet)) {
            $identifierSet = $this->getIdentifierSetIndex($identifierSet);
        }
        $product->removeIdentifierSet($identifierSet);
    }

    protected function getIdentifierSetIndex($identifierSet)
    {
        $index = null;
        $primaryIdentifier = $this->getPrimaryIdentifier();
        foreach ($identifierSet->getIdentifiers() as $node) {
            if ($node instanceof $primaryIdentifier) {
                $index = $node->getCode();
            }
        }
        if (!$index) {
            throw new \UnexpectedValueException(
                'The primary identifier is not in this Vespolina\ProductBundle\Node\ProductIdentifierSet'
            );
        }
        return $index;
    }
}
