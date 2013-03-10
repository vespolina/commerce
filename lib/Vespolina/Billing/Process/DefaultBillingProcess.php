<?php
/**
 * (c) 2013 Vespolina Project http://www.vespolina-project.org
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Vespolina\Billing\Process;

use Vespolina\Entity\Billing\BillingAgreementInterface;
use Vespolina\Entity\Billing\BillingRequestInterface;
use Vespolina\Billing\Process\BillingProcessInterface;
use Vespolina\Billing\Manager\BillingManagerInterface;
use Vespolina\Billing\Handler\DefaultBillingRequestGenerator;

class DefaultBillingProcess implements BillingProcessInterface
{
    protected $billingManager;

    public function __construct(BillingManagerInterface $billingManager, $config = array())
    {

        $defaultConfig = array('generate_first_billing_request' => true,
                               'execute_first_billing_request'  => true);

        $this->config = array_merge($defaultConfig, $config);
        $this->billingManager = $billingManager;
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

        $billingAgreements = $entityHandler->createBillingAgreements($entity);

        //Persist generated billing agreements
        foreach ($billingAgreements as $billingAgreement) {

            $this->gateway->updateBillingAgreement($billingAgreement);
        }

        //Optionally create the first billing requests
        if ($this->config['generate_first_billing_request']) {
            $billingRequestGenerator = new DefaultBillingRequestGenerator($this);
            $billingRequests = $billingRequestGenerator->generateNext($billingAgreements);

            foreach ($billingRequests as $billingRequest) {
                $this->gateway->updateBillingRequest($billingRequest);
            }
        }

            return array($billingAgreements, $billingRequests);
    }

    public function executeBilling(array $billingAgreements)
    {
        //1. Check if the billing agreement is still billable (eg. there is no billing block )
        //2. Check if we have already a billing request ready to bill
        //3. If not generate new billing requests
        //4. Offer billing requests to the payment gateway
        //5. Handle payment outcome and raise events
    }

    public function isCompleted($entity)
    {

    }

    protected function getEntityHandler($entity)
    {
        //Todo make configurable
        return new \Vespolina\Billing\Handler\OrderHandler($this->billingManager);
    }
}
