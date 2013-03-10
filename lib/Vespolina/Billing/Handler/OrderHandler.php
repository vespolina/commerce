<?php
/**
 * (c) 2013 Vespolina Project http://www.vespolina-project.org
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Vespolina\Billing\Handler;

use Vespolina\Entity\Billing\BillingAgreementInterface;
use Vespolina\Entity\Billing\BillingRequestInterface;
use Vespolina\Billing\Handler\EntityHandlerInterface;
use Vespolina\Entity\Order\OrderInterface;

class OrderHandler implements EntityHandlerInterface
{

    protected $billingManager;

    public function __construct(BillingManagerInterface $billingManager)
    {
        $this->billingManager = $billingManager;
    }

    public function createBillingAgreements($entity)
    {

        if (!$this->isBillable($entity)) {
            throw new \ErrorException('Entity is not billable');
        }

    }

    public function cancelBilling($entity)
    {

    }

    public function isBillable($entity)
    {
        //Currenlty we only check if the entity is valid but we should additional business logic here
        // (eg. if the order is cancelled it should not eligable for billing)

        return $this->isBillableEntity($entity);
    }

    protected function isBillableEntity($entity)
    {
        return (null !== $entity && $entity instanceof OrderInterface);
    }
}
