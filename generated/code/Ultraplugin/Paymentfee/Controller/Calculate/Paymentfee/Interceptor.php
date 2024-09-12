<?php
namespace Ultraplugin\Paymentfee\Controller\Calculate\Paymentfee;

/**
 * Interceptor class for @see \Ultraplugin\Paymentfee\Controller\Calculate\Paymentfee
 */
class Interceptor extends \Ultraplugin\Paymentfee\Controller\Calculate\Paymentfee implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Framework\App\Action\Context $context, \Magento\Checkout\Model\Session $checkoutSession, \Magento\Framework\Controller\Result\JsonFactory $resultJson, \Magento\Quote\Api\CartRepositoryInterface $quoteRepository, \Magento\Framework\Serialize\Serializer\Json $json)
    {
        $this->___init();
        parent::__construct($context, $checkoutSession, $resultJson, $quoteRepository, $json);
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
