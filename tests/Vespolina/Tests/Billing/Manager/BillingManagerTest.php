<?php

namespace Vespolina\Billing\Tests\Manager;

use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\DependencyInjection\Container;

use Vespolina\Billing\Manager\BillingManager;
use Vespolina\Billing\Gateway\BillingGateway;
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
class BillingManagerTest extends \PHPUnit_Framework_TestCase
{
    protected $billingGateway;

    public function testConstruct()
    {
        $this->markTestIncomplete('tests are need to make sure the contexts are set up correctly');
    }

    public function testCreateBillingAgreement()
    {
        $billingManager = $this->createBillingManager();
        $billingAgreement = $billingManager->createBillingAgreement();

        $this->assertTrue($billingAgreement instanceof BillingAgreementInterface);
    }

    public function testBillEntity()
    {
        $billingManager = $this->createBillingManager();
        $recurringOrder = $this->createRecurringOrder();
        $outcome = $billingManager->billEntity($recurringOrder);
        $billingAgreements = $outcome[0];

        $this->assertCount(1, $billingAgreements , 'there needs to be 1 billing agreement');


        foreach ($billingAgreements as $agreement) {
            $orderItems = $agreement->getOrderItems();
            if (count($orderItems) == 1) {
                // test monthly charge
                $dueDate = new \DateTime('today');
                $date = explode(',', $dueDate->format('Y,m'));
                $dueDate->setDate($date[0], $date[1]+1, 20);

                $this->assertEquals('79.90', $agreement->getPricing()->getTotalValue());
                $this->assertEquals('0', $agreement->getBillingCycles());
                $this->assertEquals('1 month', $agreement->getBillingInterval());
                $this->assertEquals($dueDate, $agreement->getInitialBillingDate());
                $this->assertEquals($dueDate, $agreement->getNextBillingDate());
            } else {
                // test annual charge
                /**$dueDate = new \DateTime('today');
                $date = explode(',', $dueDate->format('Y,m'));
                $dueDate->setDate($date[0]+1, $date[1], 20);

                $this->assertEquals('450', $agreement->getPricing()->getTotalValue());
                $this->assertEquals('-1', $agreement->getBillingCycles());
                $this->assertEquals('1 year', $agreement->getBillingInterval());
                $this->assertEquals($dueDate, $agreement->getInitialBillingDate());
                $this->assertEquals($dueDate, $agreement->getNextBillingDate());*/
            }
        }

        return;

        $this->markTestIncomplete('test for non-recurring items in order');
        $this->markTestIncomplete('test for recurring ending at different times');
        $this->markTestIncomplete('test for context w/o due date');
    }

    public function testGenerateRequestFromAgreement()
    {
        $this->markTestIncomplete('refactor');

        $order = $this->createTestOrder();
        $invoicePayment = new Invoice();
        $partner = $order->getPartner();
        $partner->setPreferredPaymentProfile($invoicePayment);
        $billingManager = $this->createBillingManager();
        $billingManager->createBillingAgreements($order);

        $agreementQuery = $this->billingGateway->createQuery('Select', 'Vespolina\Entity\Billing\BillingAgreement');
        $billingAgreements = $this->billingGateway->findBillingAgreements($agreementQuery);

        $this->getMolino()->save($invoicePayment);

        // mess with the date & payment object for the test
        foreach ($billingAgreements as $agreement) {
            $agreement->setNextBillingDate(new \DateTime('yesterday'));
            $agreement->setPaymentProfile($invoicePayment);
            $this->billingGateway->updateBillingAgreement($agreement);
        }

        $billingManager->createBillingRequest(array_shift($billingAgreements));
        $query = $this->billingGateway->createQuery('Select', 'Vespolina\Entity\Billing\BillingRequest');
        $billingRequests = $this->billingGateway->findBillingRequests($query);

        $this->assertCount(1, $billingRequests, 'consolidated into a single request');
        $request = array_shift($billingRequests);
        $this->assertEquals(79.90, $request->getPricing()->getTotalValue());

        $updatedAgreements = $this->billingGateway->findBillingAgreements($agreementQuery);
        $yesterday = new \DateTime('yesterday');
        foreach ($updatedAgreements as $agreement) {
            $this->assertGreaterThan($yesterday->getTimestamp(), $agreement->getNextBillingDate()->getTimestamp(), 'the next billing date of the agreements should have been updated');
        }

        $this->markTestIncomplete('updating a billing agreement should only affect the Partner in the originally submitted agreement');
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
            'defaultBillingProcessClass' => 'Vespolina\Billing\Process\DefaultBillingProcess'
        );
        $contexts = array();

        return new BillingManager($billingGateway, $classMapping, $contexts, $eventDispatcher);

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
