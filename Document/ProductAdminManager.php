<?php
/**
 * (c) 2011 Vespolina Project http://www.vespolina-project.org
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */
namespace Vespolina\ProductBundle\Document;

use Doctrine\ODM\MongoDB\DocumentManager;
use Symfony\Component\DependencyInjection\Container;

use Vespolina\ProductBundle\Model\ProductAdminManager as BaseProductAdminManager;
/**
 * @author Richard Shank <develop@zestic.com>
 */
class ProductAdminManager extends BaseProductAdminManager
{
    protected $dm;
    protected $optionGroupRepo;

    public function __construct(DocumentManager $dm, $optionGroupClass)
    {
        $this->dm = $dm;
        $this->optionGroupRepo = $this->dm->getRepository($optionGroupClass);
        parent::__construct($optionGroupClass);
    }

    /**
     * @inheritdoc
     */
    public function findOptionGroupsBy(array $criteria = array(), array $orderBy = null, $limit = null, $offset = null)
    {
        return $this->optionGroupRepo->findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * @inheritdoc
     */
    public function findOptionGroupsData(array $orderBy = null, $limit = null, $offset = null)
    {
        $qb = $this->dm->createQueryBuilder($this->optionGroupClass);
        $qb->hydrate(false);
        if($limit) {
            $qb->limit($limit);
        }
        if($offset) {
            $qb->skip($offset);
        }
        if($orderBy) {
            $qb->sort(key($orderBy), $orderBy);
        }

        return $qb->getQuery()
            ->execute();
    }

    /**
     * @inheritdoc
     */
    public function findOptionGroupById($id)
    {
        return $this->optionGroupRepo->find($id);
    }

    /**
     * @inheritdoc
     */
    function deleteOptionGroupById($id, $andFlush = true)
    {
        if ($group = $this->optionGroupRepo->find($id))
        {
            $this->dm->remove($group);
            if ($andFlush) {
                $this->dm->flush();
            }
        }
    }

    /**
     * @inheritdoc
     */
    function delete($object, $andFlush = true)
    {
        $this->dm->remove($object);
        if ($andFlush) {
            $this->dm->flush();
        }
    }

    /**
     * @inheritdoc
     */
    public function update($object, $andFlush = true)
    {
        $this->dm->persist($object);
        if ($andFlush) {
            $this->dm->flush();
        }
    }
}
