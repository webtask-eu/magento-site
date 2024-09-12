<?php
namespace Magento\Sales\Model\CronJob\CleanExpiredOrders;

/**
 * Interceptor class for @see \Magento\Sales\Model\CronJob\CleanExpiredOrders
 */
class Interceptor extends \Magento\Sales\Model\CronJob\CleanExpiredOrders implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Store\Model\StoresConfig $storesConfig, \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $collectionFactory, ?\Magento\Sales\Api\OrderManagementInterface $orderManagement = null)
    {
        $this->___init();
        parent::__construct($storesConfig, $collectionFactory, $orderManagement);
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'execute');
        return $pluginInfo ? $this->___callPlugins('execute', func_get_args(), $pluginInfo) : parent::execute();
    }
}
