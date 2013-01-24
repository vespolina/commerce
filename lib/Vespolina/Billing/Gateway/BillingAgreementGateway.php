<?php

namespace Vespolina\Billing\Gateway;

use Molino\MolinoInterface;
use Molino\SelectQueryInterface;
use Vespolina\Entity\Billing\BillingAgreementInterface;
use Vespolina\Exception\InvalidInterfaceException;

class BillingAgreementGateway implements BillingAgreementGatewayInterface
{
    protected $molino;
    protected $billingAgreementClass;

    /**
     * @param \Molino\MolinoInterface $molino
     * @param string $managedClass
     */
    public function __construct(MolinoInterface $molino, $billingAgreementClass)
    {
        if (!class_exists($billingAgreementClass) || !in_array('Vespolina\Entity\BillingAgreement\BillingAgreementInterface', class_implements($billingAgreementClass))) {
            throw new InvalidInterfaceException('Please have your billingAgreement class implement Vespolina\Entity\BillingAgreement\BillingAgreementInterface');
        }
        $this->molino = $molino;
        $this->billingAgreementClass = $billingAgreementClass;
    }

    /**
     * @inheritdoc
     */
    public function createQuery($type, $queryClass = null)
    {
        $type = ucfirst(strtolower($type));
        if (!in_array($type, array('Delete', 'Select', 'Update'))) {
            throw new InvalidArgumentException($type . ' is not a valid Query type');
        }
        $queryFunction = 'create' . $type . 'Query';

        if (!$queryClass) {
            $queryClass = $this->billingAgreementClass;
        }
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
}
