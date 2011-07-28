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
use Vespolina\ProductBundle\Model\Node\ProductIdentifiersInterface;

/**
 * @author Richard Shank <develop@zestic.com>
 */
abstract class ProductManager implements ProductManagerInterface
{
    protected $primaryIdentifier;

    /**
     * @inheritdoc
     */
    public function getPrimaryIdentifier()
    {
        return $this->primaryIdentifier;
    }

    /**
     * @inheritdoc
     */
    public function addIdentifiersToProduct(ProductIdentifiersInterface $identifiers, ProductInterface $product)
    {
        if (!$this->primaryIdentifier) {
            throw new \UnexpectedValueException('The primary identifier type has not been set');
        }
        foreach ($identifiers->getIdentifiers() as $node) {
            if ($node instanceof $this->primaryIdentifier) {
                $index = $node->getCode();
            }
        }
        if (!$index) {
            throw new UnexpectedValueException(
                'The primary identifier is not in this Vespolina\ProductBundle\Node\ProductIdentifiers instance'
            );
        }
        $this->product->addIdentifiers($index, $identifiers);
    }

    /**
     * @inheritdoc
     */
    public function removeIdentifiersFromProduct($identifiers, ProductInterface $product)
    {

    }
}
