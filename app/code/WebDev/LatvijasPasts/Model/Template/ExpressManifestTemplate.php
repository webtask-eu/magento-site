<?php

namespace WebDev\LatvijasPasts\Model\Template;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory;

class ExpressManifestTemplate extends Template
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
            ->addFieldToFilter('shipping_method', ['like' => 'latvijaspasts_%'])
            ->addFieldToFilter('shipping_method', ['nlike' => '%MANS_PASTS_COURIER'])
            ->addFieldToFilter('state', ['neq' => 'canceled'])
            ->load();
    }
}
