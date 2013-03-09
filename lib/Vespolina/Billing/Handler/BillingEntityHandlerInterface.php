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
interface BillingEntityHandlerInterface
{

    /**
     * Create the necessary billing agreement for this entity
     *
     * @param $entity
     * @return mixed
     */
    function createBillingAgreements($entity);

    /**
     * Cancel the billing process for this entity
     *
     * @param $entity
     * @return mixed
     */
    function cancelBilling($entity);

}
