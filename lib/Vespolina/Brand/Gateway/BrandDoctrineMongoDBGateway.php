<?php

/**
 * (c) 2011 - ∞ Vespolina Project http://www.vespolina-project.org
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Vespolina\Brand\Gateway;

use Doctrine\ODM\MongoDB\DocumentManager;
use Vespolina\Entity\Brand\BrandInterface;
use Vespolina\Exception\InvalidInterfaceException;
use Vespolina\Specification\SpecificationInterface;

/**
 * Defines a gateway to MongoDB using Doctrine ODM
 *
 * @author Daniel Kucharski <daniel@xerias.be>
 * @author Richard Shank <develop@zestic.com>
 */
class BrandDoctrineMongoDBGateway extends BrandGateway
{
    protected $dm;

    /**
     * @param DocumentManager $documentManager
     * @param $brandClass
     */
    public function __construct(DocumentManager $documentManager, $brandClass)
    {
        $this->dm = $documentManager;
        parent::__construct($brandClass, 'DoctrineMongoDB');
    }

    /**
     * @inheritdoc
     */
    public function createQuery()
    {
        return $this->dm->createQueryBuilder($this->brandClass);
    }

    /**
     * @inheritdoc
     */
    public function deleteBrand(BrandInterface $brand, $andFlush = false)
    {
        $this->dm->remove($brand);
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
    public function persistBrand(BrandInterface $brand, $andFlush = false)
    {
        $this->dm->persist($brand);
        if ($andFlush) {
            $this->flush();
        }
    }

    /**
     * @inheritdoc
     */
    public function updateBrand(BrandInterface $brand, $andFlush = false)
    {
        $this->dm->persist($brand);
        if ($andFlush) {
            $this->flush();
        }
    }

    /**
     * Execute the requested specification and return one or multiple brands (as a collection)
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

    protected function getVisitor()
    {
        return new \Vespolina\Brand\Specification\Visitor\DoctrineMongoDBDefaultSpecificationVisitor();
    }
}
