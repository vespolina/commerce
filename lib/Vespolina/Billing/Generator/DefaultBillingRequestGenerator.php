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
            // Determine how many billing requests should be created for this billing agreement
            // However if the serie is the billing agreement is infinite  we
            // should stop when generate_billing_requests_limit is reached
            $cyclesToGenerate = $billingAgreement->getBillingCycles();
            if ($cyclesToGenerate == -1) {
                $cyclesToGenerate = $this->config['generate_billing_requests_limit'];
            }

            for ($i = 0; $i < $cyclesToGenerate; $i++) {

                $billingRequest = $this->generateNext($billingAgreement);

                if (null == $billingRequest) {
                    break;
                }
                $this->billingManager->updateBillingRequest($billingRequest);
                $generatedBillingRequests[] = $billingRequest;
            }
        }

        return $generatedBillingRequests;
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
                $plannedBillingDate = $billingPeriodEnd->add(new \DateInterval('P1D'));  //Add one day
            } else if ($this->config['billing_request_payment_phase'] == 'start') {
                $plannedBillingDate = $billingPeriodStart;
            } else {
                throw new \Exception('billing_request_payment_phase option "' .  $this->config['billing_request_payment_phase'] . '" is unknown');
            }
            $billingRequest->setPlannedBillingDate($plannedBillingDate);

            //After we've created a billing request we need to update the billed-to date of the billing agreement
            $billingAgreement->setBilledToDate($billingPeriodEnd);
        }

        return $billingRequest;
    }

    public function getNextBillingPeriod(BillingAgreementInterface $billingAgreement)
    {

        //Find out to which date the billing agreement was already executed (eg. a billing request was already made)
        $startDate = $billingAgreement->getBilledToDate();

        if (null == $startDate) {
            //If never billed: use the initial billing date as starting point
            $startDate = clone $billingAgreement->getInitialBillingDate();
        } else {
            //Increase last billed to with one day; that will be the start date of the next period
            $startDate = clone $startDate->add(new \DateInterval('P1D'));
        }

        //Determine end date
        $endDate =  $this->addMonth($startDate, 1); //TODO: allow more then just months

        return array($startDate, $endDate);
    }

    protected function addMonth($date, $monthCount)
    {
        //Credits go to http://stackoverflow.com/a/10837016
        $newDate = clone $date;
        $myDayOfMonth = date_format($newDate, 'j');
        $newDate->modify("+$monthCount months");

        //Find out if the day-of-month has dropped
        $myNewDayOfMonth = date_format($newDate,'j');
        if ($myDayOfMonth > 28 && $myNewDayOfMonth < 4){
            //If so, fix by going back the number of days that have spilled over
            $newDate->modify("-$myNewDayOfMonth days");
        }

        return $newDate;
    }

}
