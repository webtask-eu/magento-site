<?php

namespace WebDev\LatvijasPasts\Model\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Shipping\Block\Adminhtml\Order\Packaging;
use WebDev\LatvijasPasts\Block\Adminhtml\Order\Packaging\Select;

class AddSelectToPackagingBlockObserver implements ObserverInterface
{
    /**
     * @throws LocalizedException
     */
    public function execute(Observer $observer)
    {
        $block = $observer->getEvent()->getData('block');
        if (!$block instanceof Packaging) {
            return;
        }

        $transport = $observer->getEvent()->getData('transport');
        $html = $transport->getHtml();
        $additional = $block->getLayout()->createBlock(Select::class)->toHtml();
        $html = str_replace('<div id="packaging_window">', '<div id="packaging_window">' . $additional, $html);
        $transport->setHtml($html);
    }
}
