<?php

/*
 * (c) 2011 - ∞ Vespolina Project http://www.vespolina-project.org
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Vespolina\Product\Gateway;

use Doctrine\ODM\PHPCR\DocumentManager;
use Vespolina\Entity\Product\ProductInterface;
use Vespolina\Exception\InvalidInterfaceException;
use Vespolina\Product\Specification\SpecificationInterface;

class ProductDoctrinePHPCRGateway extends ProductGateway
{

    protected $dm;

    /**
     * @param $documentManager
     * @param $productClass
     */
    public function __construct(DocumentManager $documentManager, $productClass)
    {
        $this->dm = $documentManager;
        parent::__construct($productClass, 'DoctrinePHPCR');
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
    public function findOne(SpecificationInterface $specification)
    {
        return $this->executeSpecification($specification, true);
    }

    /**
     * @inheritdoc
     */
    public function findAll(SpecificationInterface $specification)
    {
        return $this->executeSpecification($specification, false);
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
    public function flush()
    {
        $this->dm->flush();
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

        //Assure a product parent exists, if it doesn't exists use the first taxonomy node as parent
        if (null == $product->getParent()) {
            foreach ($product->getTaxonomies() as $taxonomyNode) {
                $product->setParent($taxonomyNode);
                break;
            }
        }

        $this->dm->persist($product);
        if ($andFlush) {
            $this->flush();
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
