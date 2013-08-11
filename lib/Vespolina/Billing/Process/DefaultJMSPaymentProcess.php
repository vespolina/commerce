<?php
/**
 * (c) 2013 Vespolina Project http://www.vespolina-project.org
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Vespolina\Billing\Process;

use JMS\Payment\CoreBundle\Entity\ExtendedData;
use JMS\Payment\CoreBundle\PluginController\Result;
use Vespolina\Billing\Process\PaymentProcessInterface;
use Vespolina\Entity\Billing\BillingRequestInterface;
use Vespolina\Entity\Partner\PaymentProfileType\CreditCard;
use Vespolina\EventDispatcher\EventDispatcherInterface;

class DefaultJMSPaymentProcess implements PaymentProcessInterface
{
    protected $billingProcess;
    protected $dispatcher;

    public function __construct(BillingProcessInterface $billingProcess, $dispatcher, $config = array())
    {

        $defaultConfig = array('retry' => true,
                                'execute_first_billing_request'  => true);

        $this->config = array_merge($defaultConfig, $config);
        $this->dispatcher = $dispatcher;
        $this->$billingProcess = $billingProcess;
    }


    public function executePayment(BillingRequestInterface $billingRequest)
    {
        $paymentProfile = $billingRequest->getPaymentProfile();

        if (null !== $paymentProfile && $paymentProfile instanceof CreditCard) {
            $totalValue = $billingRequest->getPricingSet()->get('totalValue');
            $ex = new ExtendedData();
            $ex->set('cardId', $paymentProfile->getReference(), false, false);

            /** $result = $this->getBillingService()->doStraightPayment($totalValue, $ex);

            if ($result->getStatus() == Result::STATUS_SUCCESS) {
                $billingRequest->setStatus(BillingRequest::STATUS_PAID);

                $this->gateway->updateBillingRequest($billingRequest);
            } */
        }
    }
}
