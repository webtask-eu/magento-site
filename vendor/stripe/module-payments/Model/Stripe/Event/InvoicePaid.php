<?php

namespace StripeIntegration\Payments\Model\Stripe\Event;

class InvoicePaid extends \StripeIntegration\Payments\Model\Stripe\Event
{
    public function process($arrEvent, $object)
    {
        // Avoid mutating orders in multiple events, it can lead to race conditions and data corruption
    }
}