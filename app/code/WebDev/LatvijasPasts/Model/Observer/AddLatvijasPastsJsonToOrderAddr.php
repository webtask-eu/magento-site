<?php

namespace WebDev\LatvijasPasts\Model\Observer;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;

class AddLatvijasPastsJsonToOrderAddr implements ObserverInterface
{
    public function execute(EventObserver $observer)
    {
        $order = $observer->getOrder();
        $quote = $observer->getQuote();
        $quoteShippingAddress = $quote->getShippingAddress();
        $orderShippingAddress = $order->getShippingAddress();
        $orderShippingAddress->setLatvijasPastsJson($quoteShippingAddress->getLatvijasPastsJson());
        return $this;
    }
}
