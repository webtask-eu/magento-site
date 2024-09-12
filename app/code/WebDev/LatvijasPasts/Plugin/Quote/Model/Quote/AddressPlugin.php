<?php

namespace WebDev\LatvijasPasts\Plugin\Quote\Model\Quote;

use Magento\Quote\Model\Quote\Address;
use WebDev\LatvijasPasts\Model\Carrier;

class AddressPlugin
{
    private Carrier $carrier;

    public function __construct(
        Carrier $carrier
    ) {
        $this->carrier = $carrier;
    }

    public function beforeBeforeSave(Address $subject)
    {
        $allowed = $this->carrier->getAllAvailableMethodsForPickupWithCode();
        if (
            in_array($subject->getShippingMethod(), $allowed) &&
            $subject->getExtensionAttributes()->getLatvijasPastsJson()
        ) {
            $subject->setLatvijasPastsJson($subject->getExtensionAttributes()->getLatvijasPastsJson());
        }
        return [];
    }
}
