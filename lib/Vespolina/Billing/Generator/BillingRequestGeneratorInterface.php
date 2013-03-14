<?php
/**
 * (c) 2012 - 2013 Vespolina Project http://www.vespolina-project.org
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Vespolina\Billing\Generator;

use Vespolina\Entity\Billing\BillingRequestInterface;
use Vespolina\Entity\Billing\BillingAgreementInterface;

/**
 * An interface to generate new billing requests for a billing agreement
 *
 * @author Daniel Kucharski <daniel@xerias.be>
 */
interface BillingRequestGeneratorInterface
{
    /*
     *  Generate all billing requests for a collection of billing agreements
     *
     *
     * @return array \Vespolina\Entity\Billing\BillingRequestInterface
     */

    function generate(array $billingAgreements);

    /**
     * Generate the next (or first) billing request for provided billing agreement
     *
     * @param $billingAgreement
     * @return mixed
     */
    function generateNext(BillingAgreementInterface $billingAgreement);

    /**
     * Determine the next billing period (start date + end date)
     *
     * @param BillingAgreementInterface $billingAgreement
     * @return mixed
     */
    function getNextBillingPeriod(BillingAgreementInterface $billingAgreement);
}