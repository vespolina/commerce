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
use Vespolina\Billing\Manager\BillingManagerInterface;
use Vespolina\Entity\Order\OrderInterface;
use Vespolina\Entity\Order\ItemInterface;

class OrderHandler implements EntityHandlerInterface
{

    protected $billingManager;

    public function __construct(BillingManagerInterface $billingManager)
    {
        $this->billingManager = $billingManager;
    }

    public function createBillingAgreements($entity)
    {
        $billingAgreements = array();
        $recurringItems = array();          //Initial set of detected recurring items
        $recurringItemsMerged = array();    //Final set of recurring items after merge

        if (!$this->isBillable($entity)) {
            throw new \ErrorException('Entity is not billable');
        }

        //Collect items for which a recurring charge exists
        /** @var Item $item **/
        foreach ($entity->getItems() as $item) {
            $pricingSet = $item->getPricing();
            $pricingSet->getProcessed();

            if ($pricingSet->get('recurringCharge')) {

                $recurringItems[] = $item;
            }
        }

        //Todo: merge items together
        $recurringItemsMerged = $recurringItems;
        foreach ($recurringItemsMerged as $recurringItem) {

            // Check if we can attach this item to one of the existing billing agreements.
            // If no suitable agreement can be found a new one is created
            // If a suitable agreement is found the order item is attached to it
            $this->createOrUpdateBusinessAgreements($billingAgreements, $recurringItem);

        }

        return $billingAgreements;
    }

    public function cancelBilling($entity)
    {

    }

    public function initBillingAgreement(BillingAgreementInterface $billingAgreement, $entity, $entityItem = null)
    {
        /** @var PartnerInterface $owner **/
        $owner = $entity->getOwner();

        //$paymentProfile = $owner->getPreferredPaymentProfile();
        $billingAgreement
            ->setOwner($owner)
            //->setPaymentProfile($owner->getPaymentProfile())
        ;
    }

    public function isBillable($entity)
    {
        if (!$this->isBillableEntity($entity)) return false;

        if (null == $entity->getOwner()) return false;

        return true;
    }

    protected function createOrUpdateBusinessAgreements(array &$agreements, ItemInterface $item, $context = null)
    {
        $pricingSet = $item->getPricing();
        $interval = $pricingSet->get('interval');
        $cycles = $pricingSet->get('cycles');

        if ($item->getAttribute('start_billing')) {
            $startsOn = $item->getAttribute('start_billing');
        } elseif ($context['dueDate']) {
            $startsOn = $pricingSet->get('startsOn');
            $date = explode(',', $startsOn->format('Y,m'));
            $startsOn->setDate($date[0], $date[1], $context['dueDate']);
        } else {
            $startsOn = new \DateTime($pricingSet->get('startsOn'));
        }
        $startTimestamp = $startsOn->getTimestamp();

        //Find a suitable billing agreement
        $activeAgreement = null;
        foreach ($agreements as $agreement) {
            if ($agreement->getBillingInterval() == $interval &&
                $agreement->getBillingCycles() == $cycles &&
                $agreement->getInitialBillingDate()->getTimestamp() == $startTimestamp) {
                $activeAgreement = $agreement;
            }
        }

        if (null == $activeAgreement) {
            $activeAgreement = $this->billingManager->createBillingAgreement();
            $this->initBillingAgreement($activeAgreement, $item->getParent(), $item);
            $activeAgreement
                ->setInitialBillingDate($startsOn)
                ->setBillingCycles($pricingSet->get('cycles'))
                ->setBillingInterval($pricingSet->get('interval'));
            ;
            $agreements[] = $activeAgreement;

        }

        //$activeAgreement->addOrderItem($item);
        //$activePricingSet = $activeAgreement->getPricing();
        //$activeAgreement->setPricing($pricingSet->plus($activePricingSet));

        return $activeAgreement;
    }

    protected function isBillableEntity($entity)
    {
        return (null !== $entity && $entity instanceof OrderInterface);
    }
}
