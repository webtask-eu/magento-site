<?php
namespace Magento\Sales\Model\ResourceModel\Collection\ExpiredQuotesCollection;

/**
 * Interceptor class for @see \Magento\Sales\Model\ResourceModel\Collection\ExpiredQuotesCollection
 */
class Interceptor extends \Magento\Sales\Model\ResourceModel\Collection\ExpiredQuotesCollection implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Framework\App\Config\ScopeConfigInterface $config, \Magento\Quote\Model\ResourceModel\Quote\CollectionFactory $collectionFactory)
    {
        $this->___init();
        parent::__construct($config, $collectionFactory);
    }

    /**
     * {@inheritdoc}
     */
    public function getExpiredQuotes(\Magento\Store\Api\Data\StoreInterface $store) : \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getExpiredQuotes');
        return $pluginInfo ? $this->___callPlugins('getExpiredQuotes', func_get_args(), $pluginInfo) : parent::getExpiredQuotes($store);
    }
}
