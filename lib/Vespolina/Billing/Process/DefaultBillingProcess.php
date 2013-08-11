<?php
/**
 * (c) 2013 Vespolina Project http://www.vespolina-project.org
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Vespolina\Billing\Process;

use Vespolina\Billing\Event\BillingEvents;
use Vespolina\Entity\Billing\BillingAgreementInterface;
use Vespolina\Entity\Billing\BillingRequestInterface;
use Vespolina\EventDispatcher\EventDispatcherInterface;
use Vespolina\Billing\Manager\BillingManagerInterface;
use Vespolina\Billing\Process\BillingProcessInterface;

class DefaultBillingProcess implements BillingProcessInterface
{
    protected $billingManager;
    protected $billingRequestGenerator;
    protected $classMapping;
    protected $eventDispatcher;
    protected $entityHandler;
    protected $processes;

    public function __construct(BillingManagerInterface $billingManager, EventDispatcherInterface $eventDispatcher, $config = array())
    {

        $this->classMapping = array('billingRequestGenerator'   => 'Vespolina\Billing\Generator\DefaultBillingRequestGenerator',
                                    'entityHandler'             => 'Vespolina\Billing\Handler\OrderHandler',
                                    'paymentProcess'            => 'Vespolina\Billing\Process\DefaultJMSPaymentProcess');
        $defaultConfig = array('generate_first_billing_request' => true,
                               'execute_first_billing_request'  => true);

        $this->billingManager = $billingManager;
        $this->config = array_merge($defaultConfig, $config);
        $this->eventDispatcher = $eventDispatcher;
        $this->processes = array();
    }

    /**
     * @inheritdoc
     */
    public function prepareBilling($entity)
    {
        $billingAgreements = array();
        $billingRequests = array();

        $entityHandler = $this->getEntityHandler($entity);

        if (null == $entityHandler) {
            throw new \InvalidConfigurationException("Could not find a handler for ", get_class($entity));
        }

        if (!$entityHandler->isBillable($entity)) {
            throw new \Exception("Entity is not billable");
        }

        try {

            $billingAgreements = $entityHandler->createBillingAgreements($entity);

            //Persist generated billing agreements
            foreach ($billingAgreements as $billingAgreement) {
                $this->billingManager->updateBillingAgreement($billingAgreement);
            }

            //Optionally create the first billing requests
            if (count($billingAgreements) > 0 && $this->config['generate_first_billing_request']) {

                //Use the default billing request generator
                $billingRequests = $this->getBillingRequestGenerator()->generateNext($billingAgreements);

                foreach ($billingRequests as $billingRequest) {
                    $this->billingManager->updateBillingRequest($billingRequest);
                }
            }
        } catch (\Exception $e) {

        }

        return array($billingAgreements, $billingRequests);
    }

    public function executeBilling(array $billingAgreements)
    {
        foreach ($billingAgreements as $billingAgreement) {

            $billingRequests = null;

            //1. Check if the billing agreement is still billable (eg. there is no billing block )
            if (true) { //$billingAgreement->isBillable()) {

                //2. Check if we have already a billing request ready to bill
                //$billingRequests = $this->billingManager->findBillableBillingRequestsByBillingAgreement($billingAgreement);

                //3. If not generate a new billing requests
                if (null == $billingRequests) {
                    $billingRequests = $this->getBillingRequestGenerator()->generate(array($billingAgreement));
                }

                if (null != $billingRequests) {

                    //4. Create and fire payment request event
                    foreach ($billingRequests as $billingRequest) {

                        $event = $this->eventDispatcher->createEvent($billingRequest);
                        $this->eventDispatcher->dispatch(BillingEvents::BILLING_REQUEST_OFFER_FOR_PAYMENT, $event);
                    }
                    //5. Handle payment outcome and raise events
                }
            }
        }
    }

    public function executePendingBillingRequests()
    {
        $billingRequests = $this->billingManager->findPendingBillingRequests(); //Todo: add iterator
        $paymentProcess = $this->getProcess('payment');

        foreach ($billingRequests as $billingRequest) {
            $paymentProcess->executePayment($billingRequest);
        }
    }

    public function isCompleted($entity)
    {

    }

    protected function getBillingRequestGenerator()
    {
        if (null == $this->billingRequestGenerator) {
            $this->billingRequestGenerator = new $this->classMapping['billingRequestGenerator']($this->billingManager);
        }

        return $this->billingRequestGenerator;
    }

    protected function getEntityHandler($entity)
    {
        if (null == $this->entityHandler) {
            $this->entityHandler = new $this->classMapping['entityHandler']($this->billingManager);
        }
        return $this->entityHandler;
    }
}