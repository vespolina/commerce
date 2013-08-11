VespolinaPartnerBundle UseCases

Usecases concerning customers, suppliers, employees etc.
========================================================

We came up with a view use cases how the partner bundle can be used.

  1. `Manage customers in a webshop`_
  2. `Allow employees to sale items on a cash register`_
  3. `A business partner pays after delivery`_
  4. `You import products from a supplier`_


Manage customers in a webshop
-----------------------------

Probably the most common use. The PartnerBundle can be used to manage your customers.

.. code::

	// Create a partner from a form
	$partnerManager = $this->get('vespolina.partner_manager');
	$partner = $partnerManager->createPartner(Partner::ROLE_CUSTOMER);
	$form = $this->get('vespolina.partner.customer_form');
	$form->setData($partner);
	$form->bindRequest($this->getRequest());
	if ($form->isValid()) {
		$partnerManager->updatePartner($partner);
	}

Now we are done

Allow employees to sale items on a cash register
------------------------------------------------

A business partner pays after delivery
--------------------------------------

You import products from a supplier
-----------------------------------

