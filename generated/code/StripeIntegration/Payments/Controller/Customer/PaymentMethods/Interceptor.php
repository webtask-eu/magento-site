<?php
namespace StripeIntegration\Payments\Controller\Customer\PaymentMethods;

/**
 * Interceptor class for @see \StripeIntegration\Payments\Controller\Customer\PaymentMethods
 */
class Interceptor extends \StripeIntegration\Payments\Controller\Customer\PaymentMethods implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Framework\App\Action\Context $context, \Magento\Framework\View\Result\PageFactory $resultPageFactory, \Magento\Customer\Model\Session $session, \StripeIntegration\Payments\Model\Config $config, \StripeIntegration\Payments\Helper\Generic $helper, \Magento\Framework\Controller\ResultFactory $resultFactory, \StripeIntegration\Payments\Model\Config $stripeConfigModel)
    {
        $this->___init();
        parent::__construct($context, $resultPageFactory, $session, $config, $helper, $resultFactory, $stripeConfigModel);
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
