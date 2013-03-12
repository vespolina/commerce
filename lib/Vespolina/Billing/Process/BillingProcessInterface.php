<?php
/**
 * (c) 2012-2013 Vespolina Project http://www.vespolina-project.org
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Vespolina\Billing\Process;

use Vespolina\Entity\Billing\BillingAgreementInterface;
use Vespolina\Entity\Order\OrderInterface;

/**
 * An interface to process the billing from start to end
 *
 * @author Daniel Kucharski <daniel@xerias.be>
 */
interface BillingProcessInterface
{

     /**
     * Execute the billing for a collection of business agreements (of the same owner)
     * This action typically offers billing requests to a payment gateway
     *
     * @param $entity
     * @return mixed
     */
    function executeBilling(array $businessAgreements);

    /**
     * Execute billing requests ready to be billed
     *
     * @return mixed
     */
    function executePendingBillingRequests();

    /**
     * Prepare billing for this entity
     * This action typically consists of generating billing agreements and (optionally) billing requests
     *
     * @param $entity
     * @return mixed
     */
    function prepareBilling($entity);



    /**
     * Is the processing completed for this entity (eg. is the order fully billed)
     *
     * @param $entity
     * @return mixed
     */
    function isCompleted($entity);

}
