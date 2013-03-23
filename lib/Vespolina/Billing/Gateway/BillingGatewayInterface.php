<?php

namespace Vespolina\Billing\Gateway;

use Molino\MolinoInterface;
use Molino\SelectQueryInterface;
use Vespolina\Entity\Billing\BillingAgreementInterface;
use Vespolina\Exception\InvalidInterfaceException;
use Vespolina\Entity\Billing\BillingRequestInterface;

interface BillingGatewayInterface
{
    /**
     * @param string $type
     * @param type $queryClass
     * @return type
     * @throws InvalidArgumentException
     */
    function createQuery($type, $queryClass = null);

    /**
     * @param \Vespolina\Entity\Billing\BillingAgreementInterface $billingAgreement
     */
    function deleteBillingAgreement(BillingAgreementInterface $billingAgreement);

    /**
     * @param \Molino\SelectQueryInterface $query
     * @return \Vespolina\Entity\Billing\BillingAgreementInterface
     */
    function findBillingAgreement(SelectQueryInterface $query);

    /**
     * @param \Molino\SelectQueryInterface $query
     * @return \Vespolina\Entity\Billing\BillingAgreementInterface
     */
    function findBillingAgreements(SelectQueryInterface $query);

    /**
     * @param \Vespolina\Entity\Billing\BillingAgreementInterface $billingAgreement
     */
    function persistBillingAgreement(BillingAgreementInterface $billingAgreement);

    /**
     * @param \Vespolina\Entity\Billing\BillingAgreementInterface $billingAgreement
     */
    function updateBillingAgreement(BillingAgreementInterface $billingAgreement);

    /**
     * @param \Vespolina\Entity\Billing\BillingRequestInterface $billingRequest
     */
    function deleteBillingRequest(BillingRequestInterface $billingRequest);

    /**
     * @param \Molino\SelectQueryInterface $query
     * @return \Vespolina\Entity\Billing\BillingRequestInterface
     */
    function findBillingRequest(SelectQueryInterface $query);

    /**
     * @param \Molino\SelectQueryInterface $query
     * @return \Vespolina\Entity\Billing\BillingRequestInterface
     */
    function findBillingRequests(SelectQueryInterface $query);

    /**
     * @param \Vespolina\Entity\Billing\BillingRequestInterface $billingRequest
     */
    function persistBillingRequest(BillingRequestInterface $billingRequest);

    /**
     * @param \Vespolina\Entity\Billing\BillingRequestInterface $billingRequest
     */
    function updateBillingRequest(BillingRequestInterface $billingRequest);
}
