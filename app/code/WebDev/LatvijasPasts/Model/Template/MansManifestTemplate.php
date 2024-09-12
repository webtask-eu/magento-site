<?php

namespace WebDev\LatvijasPasts\Model\Template;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory;

class MansManifestTemplate extends Template
{
    private CollectionFactory $_orderCollectionFactory;

    public function __construct(Context $context, CollectionFactory $orderCollectionFactory)
    {
        parent::__construct($context);
        $this->_orderCollectionFactory = $orderCollectionFactory;
    }

    public function getOrders()
    {
        return $this->_orderCollectionFactory
            ->create()
            ->addFieldToFilter('shipping_method', ['eq' => 'latvijaspasts_MANS_PASTS_COURIER'])
            ->addFieldToFilter('state', ['neq' => 'canceled'])
            ->load();
    }
}
