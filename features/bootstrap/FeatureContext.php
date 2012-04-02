<?php

use Behat\Behat\Context\ClosuredContextInterface,
    Behat\Behat\Context\TranslatedContextInterface,
    Behat\Behat\Context\BehatContext,
    Behat\Behat\Exception\PendingException;
use Behat\Gherkin\Node\PyStringNode,
    Behat\Gherkin\Node\TableNode;

//
// Require 3rd-party libraries here:
//
//   require_once 'PHPUnit/Autoload.php';
//   require_once 'PHPUnit/Framework/Assert/Functions.php';
//

/**
 * Features context.
 */
class FeatureContext extends BehatContext
{
    /**
     * Initializes context.
     * Every scenario gets it's own context object.
     *
     * @param   array   $parameters     context parameters (set them up through behat.yml)
     */
    public function __construct(array $parameters)
    {
        // Initialize your context here
    }

    /**
     * @Given /^the customer has an order$/
     */
    public function theCustomerHasAnOrder()
    {
        throw new PendingException();
    }

    /**
     * @When /^I "([^"]*)" an invoice request$/
     */
    public function iAnInvoiceRequest($argument1)
    {
        throw new PendingException();
    }

    /**
     * @Given /^I "([^"]*)" the "([^"]*)" to the invoice request$/
     */
    public function iTheToTheInvoiceRequest($argument1, $argument2)
    {
        throw new PendingException();
    }

    /**
     * @Given /^I "([^"]*)" the invoice request$/
     */
    public function iTheInvoiceRequest($argument1)
    {
        throw new PendingException();
    }

    /**
     * @Then /^I should receive an invoice response$/
     */
    public function iShouldReceiveAnInvoiceResponse()
    {
        throw new PendingException();
    }

    /**
     * @Given /^the invoice response should have an "([^"]*)"$/
     */
    public function theInvoiceResponseShouldHaveAn($argument1)
    {
        throw new PendingException();
    }

    /**
     * @Given /^the "([^"]*)" should contain the "([^"]*)"$/
     */
    public function theShouldContainThe($argument1, $argument2)
    {
        throw new PendingException();
    }
}
