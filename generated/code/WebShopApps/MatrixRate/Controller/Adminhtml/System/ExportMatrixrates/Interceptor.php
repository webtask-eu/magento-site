<?php
namespace WebShopApps\MatrixRate\Controller\Adminhtml\System\ExportMatrixrates;

/**
 * Interceptor class for @see \WebShopApps\MatrixRate\Controller\Adminhtml\System\ExportMatrixrates
 */
class Interceptor extends \WebShopApps\MatrixRate\Controller\Adminhtml\System\ExportMatrixrates implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Backend\App\Action\Context $context, \Magento\Config\Model\Config\Structure $configStructure, \Magento\Config\Controller\Adminhtml\System\ConfigSectionChecker $sectionChecker, \Magento\Framework\App\Response\Http\FileFactory $fileFactory, \Magento\Store\Model\StoreManagerInterface $storeManager)
    {
        $this->___init();
        parent::__construct($context, $configStructure, $sectionChecker, $fileFactory, $storeManager);
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
