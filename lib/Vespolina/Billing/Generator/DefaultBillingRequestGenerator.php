<?php
/**
 * (c) 2013 Vespolina Project http://www.vespolina-project.org
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Vespolina\Billing\Generator;

use Vespolina\Entity\Billing\BillingAgreementInterface;
use Vespolina\Entity\Billing\BillingRequestInterface;
use Vespolina\Billing\Generator\BillingRequestGeneratorInterface;
use Vespolina\Billing\Manager\BillingManagerInterface;

/**
 *
 * A default billing request generator
 */
class DefaultBillingRequestGenerator implements BillingRequestGeneratorInterface
{

    protected $billingManager;

    public function __construct(BillingManagerInterface $billingManager)
    {
        $this->billingManager = $billingManager;
    }

    function generate(array $billingAgreements)
    {
        // TODO: Implement generate() method.
    }

    /**
     * Generate the next (or first) billing request for each billing agreement
     *
     * @param array $billingAgreements
     * @return mixed
     */
    function generateNext(array $billingAgreements)
    {
        // TODO: Implement generateNext() method.
    }

}
