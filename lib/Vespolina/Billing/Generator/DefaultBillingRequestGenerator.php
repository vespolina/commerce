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
 * The default billing request generator
 */
class DefaultBillingRequestGenerator implements BillingRequestGeneratorInterface
{

    protected $billingManager;
    protected $config;

    public function __construct(BillingManagerInterface $billingManager, array $config = array())
    {
        $this->billingManager = $billingManager;

        $defaultConfig = array( //If set to true all billing requests for a billing agreement are created
                                'generate_all_billing_requests' => true,
                                //For billing agreements with unlimited cycles the number of BR we create
                               'generate_billing_requests_limit'  => 12,
                                //When should the billing request be offered to the payment gateway
                                //(begin of billable period or at the end?)
                               'billing_request_payment_phase' => 'end'  //start or end
                         );
        $this->config = array_merge($defaultConfig, $config);
    }

    public function generate(array $billingAgreements)
    {
        $generatedBillingRequests = array();
        foreach ($billingAgreements as $billingAgreement)
        {
            // Generate at least one billing request.
            // If config auto_generate_all_billing_requests is set to true we continue creating the full BR serie
            // However if the serie is the billing agreement is infinite (no end date or max number of cycles we
            // should stop when generate_billing_requests_limit is reached per billing agreement
            do {
                $i = 0;
                $billingRequest = $this->generateNext($billingAgreement);

                if (null != $billingRequest) {
                    $i++;
                    $this->billingManager->updateBillingRequest($billingRequest);
                    $generatedBillingRequests[] = $billingRequest;
                }

            } while ( null != $billingRequest &&
                      $this->config['generate_all_billing_requests'] &&
                      $i < $this->config['generate_billing_requests_limit'] );
        }
    }

    /**
     * Generate the next billing request for provided billing agreement
     *
     * @param BillingAgreementInterface $billingAgreement
     * @return mixed
     */
    public function generateNext(BillingAgreementInterface $billingAgreement)
    {
        $billingPeriodStart = null;
        $billingPeriodEnd = null;
        $billingRequest = null;

        $nextBillingPeriod = $this->getNextBillingPeriod($billingAgreement);

        if (null !== $nextBillingPeriod) {

            list($billingPeriodStart, $billingPeriodEnd) = $nextBillingPeriod;

            $billingRequest = $this->billingManager->createBillingRequest($billingAgreement);
            $billingRequest->setPeriodStart($billingPeriodStart);
            $billingRequest->setPeriodEnd($billingPeriodEnd);

            //Determine when the billing request needs to be paid
            if ($this->config['billing_request_payment_phase'] == 'end') {
                $plannedBillingDate = $billingPeriodEnd->add(new DateInterval('P1D'));  //Add one day
            } if ($this->config['billing_request_payment_phase'] == 'start') {
                $plannedBillingDate = $billingPeriodStart;
            } else {
                throw new \Exception('billing_request_payment_phase option ' .  $this->config['billing_request_payment_phase'] . ' is unknown');
            }
            $billingRequest->setPlannedBillingDate($plannedBillingDate);
        }

        return $billingRequest;
    }

    public function getNextBillingPeriod(BillingAgreementInterface $billingAgreement)
    {

    }

}
