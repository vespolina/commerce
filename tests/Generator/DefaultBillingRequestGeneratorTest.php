<?php

namespace Vespolina\Billing\Tests\Generator;

use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\DependencyInjection\Container;

use Vespolina\Billing\Generator\DefaultBillingRequestGenerator;
use Vespolina\Billing\Manager\BillingManager;
use Vespolina\Billing\Gateway\BillingGateway;
use Vespolina\Entity\Billing\BillingAgreement;
use Vespolina\Entity\Billing\BillingAgreementInterface;
use Vespolina\EventDispatcher\EventDispatcherInterface;
use Vespolina\EventDispatcher\EventInterface;
use Vespolina\Entity\Pricing\Element\RecurringElement;
use Vespolina\Entity\Pricing\PricingSet;
use Vespolina\Entity\Order\Item;
use Vespolina\Entity\Order\Order;
use Vespolina\Entity\Product\Product;
use Vespolina\Entity\Pricing\PricingContext;
use Vespolina\Entity\Partner\Partner;
use Vespolina\Entity\Pricing\Element\TotalValueElement;
/**
 * @group ecommerce
 */
class DefaultBillingRequestGeneratorTest extends \PHPUnit_Framework_TestCase
{
    protected $billingGateway;

    public function testGetNextBillingPeriod()
    {
        $generator = $this->createDefaultGenerator($this->createBillingManager());

        // Prepare some scenarios which we want to test
        $scenarios = array (
                        array(  'cycles'            => 1,
                                'interval'          => 'month',
                                'initialBilling'    => '2013-01-31',
                                //expected outcome
                                'periodStart'       => '2013-01-31',
                                'periodEnd'         => '2013-02-28'));


        foreach ($scenarios as $scenario) {

            $billingAgreement = new BillingAgreement();
            $billingAgreement->setBillingCycles($scenario['cycles']);
            $billingAgreement->getBillingInterval($scenario['interval']);
            $billingAgreement->setInitialBillingDate(new \DateTime($scenario['initialBilling']));

            $billingPeriod = $generator->getNextBillingPeriod($billingAgreement);
            list($periodStart, $periodEnd) = $billingPeriod;

            $this->assertEquals($scenario['periodStart'], $periodStart->format('Y-m-d'));
            $this->assertEquals($scenario['periodEnd'], $periodEnd->format('Y-m-d'));

        }
    }


    public function testGenerateOneBillingRequest()
    {
        $generator = $this->createDefaultGenerator($this->createBillingManager());
        $billingAgreement = new BillingAgreement();
        $billingAgreement->setBillingCycles(1);
        $billingAgreement->setBillingInterval('month');
        $billingAgreement->setInitialBillingDate(new \DateTime());

        $billingRequests = $generator->generate(array($billingAgreement));

        $this->assertEquals(1, count($billingRequests));
    }


    protected function createBillingManager()
    {
        $billingGateway = $this->getMockBuilder('Vespolina\Billing\Gateway\BillingGatewayInterface')
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $eventDispatcher = new TestDispatcher();
        $classMapping = array(
            'billingAgreementClass' => 'Vespolina\Entity\Billing\BillingAgreement',
            'billingRequestClass' => 'Vespolina\Entity\Billing\BillingRequest',
        );
        $contexts = array();

        return new BillingManager($billingGateway, $classMapping, $contexts, $eventDispatcher);

    }

    protected function createDefaultGenerator($billingManager)
    {
        return new DefaultBillingRequestGenerator($billingManager);
    }

    protected function createRecurringOrder()
    {
        $order = new Order();
        $customer = new Partner();
        $order->setOwner($customer);

        $product1 = new Product();
        $product1->setName('product1');

        $context = new PricingContext();

        $recurringElement = new RecurringElement();
        $recurringElement->setCycles(-1);
        $recurringElement->setInterval('1 month');
        $recurringElement->setRecurringCharge('30');

        $pricingSet = new PricingSet(new TotalValueElement());
        $pricingSet->addPricingElement($recurringElement);
        $pricingSet->setProcessingState(PricingSet::PROCESSING_FINISHED);
        $pricingSet1 = $pricingSet->process($context);


        $orderItem1 = new Item($product1);

        $rp = new \ReflectionProperty($orderItem1, 'pricingSet');
        $rp->setAccessible(true);
        $rp->setValue($orderItem1, $pricingSet1);
        $rp->setAccessible(false);

        $rm = new \ReflectionMethod($order, 'addItem');
        $rm->setAccessible(true);
        $rm->invokeArgs($order, array($orderItem1));
        $rm->setAccessible(false);

        return $order;
    }
}

class TestDispatcher implements EventDispatcherInterface
{
    protected $lastEvent;
    protected $lastEventName;

    public function createEvent($subject = null)
    {
        $event = new Event($subject);

        return $event;
    }

    public function dispatch($eventName, EventInterface $event = null)
    {
        $this->lastEvent = $event;
        $this->lastEventName = $eventName;
    }

    public function getLastEvent()
    {
        return $this->lastEvent;
    }

    public function getLastEventName()
    {
        return $this->lastEventName;
    }
}

class Event implements EventInterface
{
    protected $name;
    protected $subject;

    public function __construct($subject)
    {
        $this->subject = $subject;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getSubject()
    {
        return $this->subject;
    }
}
