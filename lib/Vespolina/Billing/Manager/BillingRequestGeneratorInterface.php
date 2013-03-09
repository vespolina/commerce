<?php
/**
 * (c) 2012 Vespolina Project http://www.vespolina-project.org
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Vespolina\Billing\Manager;

use Vespolina\Entity\Billing\BillingRequestInterface;
use Vespolina\Entity\Billing\BillingAgreementInterface;

/**
 * An interface to generate new billing requests
 *
 * @author Daniel Kucharski <daniel-xerias.be>
 */
interface BillingRequestGeneratorInterface
{
    /*
     *  Generate new billing requests for a collection of billing agreements
     *
     *
     * @return array \Vespolina\Entity\Billing\BillingRequestInterface
     */

    function generate(array $billingAgreements);
}