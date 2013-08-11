<?php
/**
 * (c) 2012-2013 Vespolina Project http://www.vespolina-project.org
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Vespolina\Billing\Process;

use Vespolina\Entity\Billing\BillingRequestInterface;


/**
 * An interface to process the payment of a billing request
 *
 * @author Daniel Kucharski <daniel@xerias.be>
 */
interface PaymentProcessInterface
{

     /**
     *  Offer this billing request to the payment gateway
     *
     * @param $entity
     * @return mixed
     */
    function executePayment(BillingRequestInterface $billingRequest);

}
