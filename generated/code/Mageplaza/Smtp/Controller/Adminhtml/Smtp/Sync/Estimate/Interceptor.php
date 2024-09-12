<?php
namespace Mageplaza\Smtp\Controller\Adminhtml\Smtp\Sync\Estimate;

/**
 * Interceptor class for @see \Mageplaza\Smtp\Controller\Adminhtml\Smtp\Sync\Estimate
 */
class Interceptor extends \Mageplaza\Smtp\Controller\Adminhtml\Smtp\Sync\Estimate implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Backend\App\Action\Context $context, \Mageplaza\Smtp\Helper\EmailMarketing $emailMarketing, \Magento\Customer\Model\ResourceModel\Customer\CollectionFactory $customerCollectionFactory, \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory, \Magento\Newsletter\Model\ResourceModel\Subscriber\CollectionFactory $subscriberCollectionFactory, \Mageplaza\Smtp\Helper\Data $helperData)
    {
        $this->___init();
        parent::__construct($context, $emailMarketing, $customerCollectionFactory, $orderCollectionFactory, $subscriberCollectionFactory, $helperData);
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'execute');
        return $pluginInfo ? $this->___callPlugins('execute', func_get_args(), $pluginInfo) : parent::execute();
    }

    /**
     * {@inheritdoc}
     */
    public function dispatch(\Magento\Framework\App\RequestInterface $request)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'dispatch');
        return $pluginInfo ? $this->___callPlugins('dispatch', func_get_args(), $pluginInfo) : parent::dispatch($request);
    }
}
