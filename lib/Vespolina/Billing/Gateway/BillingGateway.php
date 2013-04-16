<?php

namespace Vespolina\Billing\Gateway;

use Doctrine\Common\Collections\ArrayCollection;
use Molino\MolinoInterface;
use Molino\SelectQueryInterface;
use Vespolina\Entity\Billing\BillingAgreementInterface;
use Vespolina\Exception\InvalidInterfaceException;
use Vespolina\Entity\Billing\BillingRequestInterface;

class BillingGateway implements BillingGatewayInterface
{
    protected $molino;
    protected $billingAgreementClass;

    /**
     * @param \Molino\MolinoInterface $molino
     * @param string $managedClass
     */
    public function __construct(MolinoInterface $molino)
    {
        $this->molino = $molino;
    }

    /**
     * @inheritdoc
     */
    public function createQuery($type, $queryClass)
    {
        $type = ucfirst(strtolower($type));
        if (!in_array($type, array('Delete', 'Select', 'Update'))) {
            throw new InvalidArgumentException($type . ' is not a valid Query type');
        }
        $queryFunction = 'create' . $type . 'Query';

        return $this->molino->$queryFunction($queryClass);
    }

    /**
     * @inheritdoc
     */
    public function deleteBillingAgreement(BillingAgreementInterface $billingAgreement)
    {
        $this->molino->delete($billingAgreement);
    }

    /**
     * @inheritdoc
     */
    public function findBillingAgreement(SelectQueryInterface $query)
    {
        return $query->one();
    }

    /**
     * @inheritdoc
     */
    public function findBillingAgreements(SelectQueryInterface $query)
    {
        return $query->all();
    }

    /**
     * @inheritdoc
     */
    public function persistBillingAgreement(BillingAgreementInterface $billingAgreement)
    {
        $this->molino->save($billingAgreement);
    }

    /**
     * @inheritdoc
     */
    public function updateBillingAgreement(BillingAgreementInterface $billingAgreement)
    {
        $this->molino->save($billingAgreement);
    }

    /**
     * @param \Vespolina\Entity\Billing\BillingRequestInterface $billingRequest
     */
    public function deleteBillingRequest(BillingRequestInterface $billingRequest)
    {
        $this->molino->delete($billingRequest);
    }

    /**
     * @param \Molino\SelectQueryInterface $query
     * @return \Vespolina\Entity\Billing\BillingRequestInterface
     */
    public function findBillingRequest(SelectQueryInterface $query)
    {
        return $query->one();
    }

    /**
     * @param \Molino\SelectQueryInterface $query
     * @return \Vespolina\Entity\Billing\BillingRequestInterface
     */
    public function findBillingRequests(SelectQueryInterface $query)
    {
        return $query->all();
    }

    /**
     * @param \Vespolina\Entity\Billing\BillingRequestInterface $billingRequest
     */
    public function persistBillingRequest(BillingRequestInterface $billingRequest)
    {
        $this->molino->save($billingRequest);
    }

    /**
     * @param \Vespolina\Entity\Billing\BillingRequestInterface $billingRequest
     */
    public function updateBillingRequest(BillingRequestInterface $billingRequest)
    {
        $this->molino->save($billingRequest);
    }
}
