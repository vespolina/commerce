<?php

namespace Vespolina\Billing\Tests\Manager;

use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\DependencyInjection\Container;

use Vespolina\Billing\Manager\BillingManager;
use Vespolina\Billing\Gateway\BillingGateway;
use Vespolina\Entity\Billing\BillingAgreementInterface;
use Vespolina\EventDispatcher\EventDispatcherInterface;
use Vespolina\EventDispatcher\EventInterface;
use Vespolina\Pricing\Entity\Element\RecurringElement;
use Vespolina\Pricing\Entity\PricingSet;

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

    public function testCreateBillingAgreements()
    {
        $order = $this->createTestOrder();
        $invoicePayment = new Invoice();
        $partner = $order->getPartner();
        $partner->setPreferredPaymentProfile($invoicePayment);

        $billingManager = $this->createBillingManager();
        $billingManager->createBillingAgreements($order);

        $query = $this->billingGateway->createQuery('Select', 'Vespolina\Entity\Billing\BillingAgreement');
        $billingAgreements = $this->billingGateway->findBillingAgreements($query);

        $this->assertCount(1, $billingAgreements, 'there needs to be 1 billing agreement');

        foreach ($billingAgreements as $agreement) {
            $orderItems = $agreement->getOrderItems();
            if (count($orderItems) == 2) {
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
                $dueDate = new \DateTime('today');
                $date = explode(',', $dueDate->format('Y,m'));
                $dueDate->setDate($date[0]+1, $date[1], 20);

                $this->assertEquals('450', $agreement->getPricing()->getTotalValue());
                $this->assertEquals('-1', $agreement->getBillingCycles());
                $this->assertEquals('1 year', $agreement->getBillingInterval());
                $this->assertEquals($dueDate, $agreement->getInitialBillingDate());
                $this->assertEquals($dueDate, $agreement->getNextBillingDate());
            }
        }

        $this->markTestIncomplete('test for non-recurring items in order');
        $this->markTestIncomplete('test for recurring ending at different times');
        $this->markTestIncomplete('test for context w/o due date');
    }

    public function testGenerateRequestFromAgreement()
    {
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

    protected function createTestOrder()
    {

        $order = $this->getOrderManager()->createOrder();
        //$order->setOwner($user->getPartner());

        $this->getOrderManager()->addProductToOrder($order, $licenses[License::PRODUCT_LICENSE_NAME], array(), 1, false);
        $this->getOrderManager()->addProductToOrder($order, $licenses[License::PRODUCT_LICENSE_NAME], array(), 1, false);
//        $orderManager->addProductToOrder($order, $upgrade);

        $event = $this->getEventDispatcher()->createEvent(array($order, null));
        $this->getEventDispatcher()->dispatch(OrderEvents::UPDATE_ORDER_PRICE, $event);

        $this->getOrderManager()->updateOrder($order);

        return $order;
    }

    protected function createBillingManager()
    {
        //$this->billingGateway = new BillingGateway($this->getMolino(), 'Vespolina\Entity\Billing\BillingAgreement');

        $billingGateway = $this->getMockBuilder('Vespolina\Billing\Gateway\BillingGateway')
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $eventDispatcher = new TestDispatcher();
        $classMapping = array(
            'billingAgreementClass' => 'Vespolina\Entity\Billing\BillingAgreement',
            'billingRequestClass' => 'Vespolina\Entity\Billing\BillingRequest',
        );
        $contexts = array();
        $manager = new BillingManager($billingGateway, $classMapping, $contexts, $eventDispatcher);

        return $manager;
    }

    protected function getMolino()
    {

    }
}
