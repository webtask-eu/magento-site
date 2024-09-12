<?php
namespace StripeIntegration\Payments\Controller\Adminhtml\Configure\Webhooks;

/**
 * Interceptor class for @see \StripeIntegration\Payments\Controller\Adminhtml\Configure\Webhooks
 */
class Interceptor extends \StripeIntegration\Payments\Controller\Adminhtml\Configure\Webhooks implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Framework\App\Action\Context $context, \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory, \StripeIntegration\Payments\Helper\WebhooksSetup $webhooksSetup)
    {
        $this->___init();
        parent::__construct($context, $resultJsonFactory, $webhooksSetup);
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
