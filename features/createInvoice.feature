Feature: create invoice
  In order to bill a customer
  As a clerk
  I need to be able to create an invoice from the customer's order

  Scenario:
    Given the customer has an order
     When I "create" an invoice request
      And I "add" the "order" to the invoice request
      And I "execute" the invoice request
     Then I should receive an invoice response
      And the invoice response should have an "invoice"
      And the "invoice" should contain the "order"

