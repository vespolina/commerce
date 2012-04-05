Feature: find invoices with upcoming due dates
  In order to get fund from customer
  As a part of the system
  I need to be able to get a list of all invoices that are due in a specific time

  Scenario:
    Given I have created an invoice due in "10" days
      And I have created an invoice due in "15" days
      And I have created an invoice due in "5" days that has been paid
      And I have created an invoice due in "20" days
     When I ask for invoices due in "15" days
     Then I should receive the invoice due in "15" days
      And I should receive the invoice due in "20" days
      And I should not receive the invoice due in "10" days

  Scenario:
    Given I have created an invoice due in "10" days
      And I have created an invoice due in "15" days
      And I have created an invoice due in "20" days
      And the invoice due in "20" days has already been sent at the "10" day mark
     When I ask for invoices due in "10" days
     Then I should receive the invoice due in "15" days
      And I should receive the invoice due in "10" days
      And I should not receive the invoice due in "20" days

