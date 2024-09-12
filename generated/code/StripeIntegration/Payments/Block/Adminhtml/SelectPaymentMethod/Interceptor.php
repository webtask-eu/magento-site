<?php
namespace StripeIntegration\Payments\Block\Adminhtml\SelectPaymentMethod;

/**
 * Interceptor class for @see \StripeIntegration\Payments\Block\Adminhtml\SelectPaymentMethod
 */
class Interceptor extends \StripeIntegration\Payments\Block\Adminhtml\SelectPaymentMethod implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\StripeIntegration\Payments\Helper\InitParams $initParams, \StripeIntegration\Payments\Helper\Generic $helper, \StripeIntegration\Payments\Helper\PaymentMethod $paymentMethodHelper, \StripeIntegration\Payments\Model\Config $paymentsConfig, \Magento\Backend\Model\Session\Quote $sessionQuote, \Magento\Backend\Block\Template\Context $context, \Magento\Framework\Registry $registry, \Magento\Framework\Data\FormFactory $formFactory, array $data = [])
    {
        $this->___init();
        parent::__construct($initParams, $helper, $paymentMethodHelper, $paymentsConfig, $sessionQuote, $context, $registry, $formFactory, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function getForm()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getForm');
        return $pluginInfo ? $this->___callPlugins('getForm', func_get_args(), $pluginInfo) : parent::getForm();
    }
}
