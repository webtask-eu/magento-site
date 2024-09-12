<?php
namespace WebDev\LatvijasPasts\Controller\Adminhtml\Courier\Call;

/**
 * Interceptor class for @see \WebDev\LatvijasPasts\Controller\Adminhtml\Courier\Call
 */
class Interceptor extends \WebDev\LatvijasPasts\Controller\Adminhtml\Courier\Call implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Backend\App\Action\Context $context, \Magento\Backend\App\ConfigInterface $backendConfig, \GuzzleHttp\ClientFactory $clientFactory, \GuzzleHttp\Psr7\ResponseFactory $responseFactory, \Magento\Framework\Controller\Result\RawFactory $rawFactory)
    {
        $this->___init();
        parent::__construct($context, $backendConfig, $clientFactory, $responseFactory, $rawFactory);
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
