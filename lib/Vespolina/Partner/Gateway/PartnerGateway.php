<?php

namespace Vespolina\Partner\Gateway;

use Molino\MolinoInterface;
use Molino\SelectQueryInterface;
use Vespolina\Entity\Partner\PartnerInterface;
use Vespolina\Exception\InvalidInterfaceException;
use Vespolina\Entity\Partner\PartnerInterface;

class PartnerGateway
{
    protected $molino;
    protected $partnerClass;

    /**
     * @param \Molino\MolinoInterface $molino
     * @param string $managedClass
     */
    public function __construct(MolinoInterface $molino, $partnerClass)
    {
        if (!class_exists($partnerClass) || !in_array('Vespolina\Entity\Partner\PartnerInterface', class_implements($partnerClass))) {
             throw new InvalidInterfaceException('Please have your partner class implement Vespolina\Entity\Partner\PartnerInterface');
        }
        $this->molino = $molino;
        $this->partnerClass = $partnerClass;
    }

    /**
     * @param string $type
     * @param type $queryClass
     * @return type
     * @throws InvalidArgumentException
     */
    public function createQuery($type, $queryClass = null)
    {
        $type = ucfirst(strtolower($type));
        if (!in_array($type, array('Delete', 'Select', 'Update'))) {
            throw new InvalidArgumentException($type . ' is not a valid Query type');
        }
        $queryFunction = 'create' . $type . 'Query';

        if (!$queryClass) {
            $queryClass = $this->partnerClass;
        }
        return $this->molino->$queryFunction($queryClass);
    }

    /**
     * @param \Vespolina\Entity\Partner\PartnerInterface $partner
     */
    public function deletePartner(PartnerInterface $partner)
    {
        $this->molino->delete($partner);
    }

    /**
     * @param \Molino\SelectQueryInterface $query
     * @return \Vespolina\Entity\Partner\PartnerInterface
     */
    public function findPartner(SelectQueryInterface $query)
    {
        return $query->one();
    }

    /**
     * @param \Molino\SelectQueryInterface $query
     * @return \Vespolina\Entity\Partner\PartnerInterface
     */
    public function findPartners(SelectQueryInterface $query)
    {
        return $query->all();
    }

    /**
     * @param \Vespolina\Entity\Partner\PartnerInterface $partner
     */
    public function persistPartner(PartnerInterface $partner)
    {
        $this->molino->save($partner);
    }

    /**
     * @param \Vespolina\Entity\Partner\PartnerInterface $partner
     */
    public function updatePartner(PartnerInterface $partner)
    {
        $this->molino->save($partner);
    }
}
