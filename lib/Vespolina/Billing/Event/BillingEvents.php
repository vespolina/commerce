<?php

namespace Vespolina\Billing\Event;

final class BillingEvents
{
    const BILLING_REQUEST_INIT = 'billing_request.init';

    const BILLING_REQUEST_OFFER_FOR_PAYMENT = 'billing_request.offer_for_payment';

    const BILLING_REQUEST_PAID = 'billing_request.paid';
}
