<?php
namespace Vespolina\Tests\Billing\Functional;

use Vespolina\Tests\Order\OrderTestsCommon;

class SimpleOrderTest extends \PHPUnit_Framework_TestCase
{
    public function testSimpleOrder()
    {
        // create order with a single item
        $order = OrderTestsCommon::createSimpleOrder();



        // create a payment profile

        // hand order and profile to manager

        // a billing request should be generated

        // processing events should be triggered

        // when the processing is completed ...

        // ... the request state should change
        // ... the payment
        // ... events should be triggered

        // post events being dispatched request should be closed
    }
}
