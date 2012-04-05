Feature: find invoices past due by a specific amount of time
  In order to find delequent invoices
  As a part of the system
  I need to be able to get a list of all invoices that where due a set amount of time ago

  Scenario:
    Given I have created an invoice due in "-10" days
      And I have created an invoice due in "-15" days
      And I have created an invoice due in "-20" days
      And I have created an invoice due in "-5" days that has been paid
    When I ask for invoices past due by at least "15" days
     Then I should receive the invoice due in "-15" days
      And I should receive the invoice due in "-20" days
      And I should not receive the invoice due in "-10" days

  Scenario:
    Given I have created an invoice due in "-10" days
      And I have created an invoice due in "-15" days
      And I have created an invoice due in "-20" days
      And the invoice due in "-20" days has already been sent at the "-10" day mark
     When I ask for invoices past due by at least "10" days
     Then I should receive the invoice due in "-15" days
      And I should receive the invoice due in "-10" days
      And I should not receive the invoice due in "-20" days

