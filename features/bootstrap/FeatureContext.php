<?php
if (!@include __DIR__ . '/../../vendor/.composer/autoload.php') {
    die(<<<'EOT'
You must set up the project dependencies, run the following commands:
wget http://getcomposer.org/composer.phar
php composer.phar install
EOT
    );
}

use Behat\Behat\Context\ClosuredContextInterface,
    Behat\Behat\Context\TranslatedContextInterface,
    Behat\Behat\Context\BehatContext,
    Behat\Behat\Exception\PendingException;
use Behat\Gherkin\Node\PyStringNode,
    Behat\Gherkin\Node\TableNode;

use Vespolina\Entity\Order;

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
    protected $order;

    /**
     * Initializes context.
     * Every scenario gets it's own context object.
     *
     * @param   array   $parameters     context parameters (set them up through behat.yml)
     */
    public function __construct(array $parameters)
    {

    }

    /**
     * @Given /^the customer has an order$/
     */
    public function theCustomerHasAnOrder()
    {
        $this->order = new Order();
    }
    /**
     * @When /^I create an invoice with the order$/
     */
    public function iCreateAnInvoiceWithTheOrder()
    {
        throw new PendingException();
    }

    /**
     * @Then /^I should receive an invoice$/
     */
    public function iShouldReceiveAnInvoice()
    {
        throw new PendingException();
    }

    /**
     * @Given /^the invoice should contain the "([^"]*)"$/
     */
    public function theInvoiceShouldContainThe($argument1)
    {
        throw new PendingException();
    }

}
