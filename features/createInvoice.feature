Feature: create invoice
  In order to bill a customer
  As a clerk
  I need to be able to create an invoice from the customer's order

  Scenario:
    Given the customer has an order
     When I create an invoice with the order
     Then I should receive an invoice
      And the invoice should contain the "order"


