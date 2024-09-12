<?php
namespace Hexamarvel\Blog\Controller\Adminhtml\Post\Save;

/**
 * Interceptor class for @see \Hexamarvel\Blog\Controller\Adminhtml\Post\Save
 */
class Interceptor extends \Hexamarvel\Blog\Controller\Adminhtml\Post\Save implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Backend\App\Action\Context $context, \Hexamarvel\Blog\Model\PostFactory $postFactory, \Magento\Framework\Filesystem $filesystem, \Magento\Framework\Image\AdapterFactory $imageFactory, \Magento\Store\Model\StoreManagerInterface $storeManager, \Hexamarvel\Blog\Helper\Data $helper)
    {
        $this->___init();
        parent::__construct($context, $postFactory, $filesystem, $imageFactory, $storeManager, $helper);
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
