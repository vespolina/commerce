<?php
/**
 * (c) 2012-2013 Vespolina Project http://www.vespolina-project.org
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Vespolina\Billing\Manager;

use Vespolina\Entity\Billing\BillingAgreementInterface;
use Vespolina\Entity\Billing\BillingRequestInterface;
use Vespolina\Entity\Order\OrderInterface;

/**
 * An interface to manage the creation of billing requests
 *
 * @author Daniel Kucharski <daniel-xerias.be>
 */
interface BillingManagerInterface
{
    /**
     * Invokes the billing process for an entity (eg. recurring order, contract, ...)
     *
     *  a)Create the necessary billing agreements
     *  b)Generate the necessary billing requests (if requested)
     *  c)Optionally send the first billing request to the payment gateway
     *
     * @return mixed
     */
    function initializeBilling($entity);

    /**
     * @return \Vespolina\Entity\Billing\BillingAgreementInterface
     */
    function createBillingAgreement();

    /**
     * Generate a new billing request from a BillingAgreement
     *
     * @param \Vespolina\Entity\Billing\BillingAgreementInterface $billingAgreement
     * @return \Vespolina\Entity\Billing\BillingRequestInterface
     */
    function generateBillingRequest(BillingAgreementInterface $billingAgreement);

    /**
     * Execute a BillingRequest to collect payment
     *
     * @param BillingRequestInterface $billingRequest
     * @return mixed
     */
    function executeBillingRequest(BillingRequestInterface $billingRequest);

    /**
     * Find billing agreements by specified fields and values
     *
     * @param array $criteria
     * @param array $orderBy
     * @param null $limit
     * @param null $offset
     *
     * @return array|\Vespolina\Entity\Order\OrderInterface|null
     */
    function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null);
}
