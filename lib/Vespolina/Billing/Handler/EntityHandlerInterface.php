<?php
/**
 * (c) 2012-2013 Vespolina Project http://www.vespolina-project.org
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Vespolina\Billing\Handler;

use Vespolina\Entity\Billing\BillingAgreementInterface;
use Vespolina\Entity\Order\OrderInterface;

/**
 * An interface to define how an entity should behave during the billing process
 *
 * @author Daniel Kucharski <daniel@xerias.be>
 */
interface EntityHandlerInterface
{

    /**
     * Create the necessary billing agreement for this entity
     *
     * @param $entity
     * @return mixed
     */
    function createBillingAgreements($entity);

    /**
     * Init is called after a billing agreement has been created
     * It copies relevant fields from the entity to the billing agreement
     *
     * @param $billingAgreement The billing agreement to be initialized
     * @param $entity     The main entity (eg. Order)
     * @param $entityItem The item of the entity (eg. OrderItem)
     * @return mixed
     */
    function initBillingAgreement(BillingAgreementInterface $billingAgreement, $entity, $entityItem = null);

    /**
     * Cancel the billing process for this entity
     *
     * @param $entity
     * @return mixed
     */
    function cancelBilling($entity);

    /**
     * Is this order (still?) billable
     *
     * @param $entity
     * @return mixed
     */
    function isBillable($entity);

}
