<?php

/**
 * (c) 2011 - ∞ Vespolina Project http://www.vespolina-project.org
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Vespolina\Product\Gateway;

use Doctrine\ODM\MongoDB\DocumentManager;
use Vespolina\Entity\Product\ProductInterface;
use Vespolina\Exception\InvalidInterfaceException;
use Vespolina\Specification\SpecificationInterface;

/**
 * Defines a gateway to MongoDB using Doctrine ODM
 *
 * @author Daniel Kucharski <daniel@xerias.be>
 * @author Richard Shank <develop@zestic.com>
 */
class ProductDoctrineMongoDBGateway extends ProductGateway
{
    protected $dm;

    /**
     * @param DocumentManager $documentManager
     * @param $productClass
     */
    public function __construct(DocumentManager $documentManager, $productClass)
    {
        $this->dm = $documentManager;
        parent::__construct($productClass, 'DoctrineMongoDB');
    }

    /**
     * @inheritdoc
     */
    public function createQuery()
    {
        return $this->dm->createQueryBuilder($this->productClass);
    }

    /**
     * @inheritdoc
     */
    public function deleteProduct(ProductInterface $product, $andFlush = false)
    {
        $this->dm->remove($product);
        if ($andFlush) {
            $this->flush();
        }
    }

    /**
     * @inheritdoc
     */
    public function flush(ProductInterface $product = null)
    {
        $this->dm->flush($product);
    }

    /**
     * @inheritdoc
     */
    public function persistProduct(ProductInterface $product, $andFlush = false)
    {
        $this->dm->persist($product);
        if ($andFlush) {
            $this->flush();
        }
    }

    /**
     * @inheritdoc
     */
    public function updateProduct(ProductInterface $product, $andFlush = false)
    {
        $this->dm->persist($product);
        if ($andFlush) {
            $this->flush($product);
        }
    }

    /**
     * Execute the requested specification and return one or multiple products (as a collection)
     *
     * @param SpecificationInterface $specification
     * @param bool $matchOne
     * @return mixed
     */
    protected function executeSpecification(SpecificationInterface $specification, $matchOne = false)
    {
        $queryBuilder = $this->createQuery();
        $this->getSpecificationWalker()->walk($specification, $queryBuilder);
        $query = $queryBuilder->getQuery();

        if ($matchOne) {

            return $query->getSingleResult();
        } else {

            return $query->execute();
        }
    }
}
