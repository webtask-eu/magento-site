<?php
namespace Magento\Multishipping\Helper\Data;

/**
 * Interceptor class for @see \Magento\Multishipping\Helper\Data
 */
class Interceptor extends \Magento\Multishipping\Helper\Data implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Framework\App\Helper\Context $context, \Magento\Checkout\Model\Session $checkoutSession)
    {
        $this->___init();
        parent::__construct($context, $checkoutSession);
    }

    /**
     * {@inheritdoc}
     */
    public function isMultishippingCheckoutAvailable()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'isMultishippingCheckoutAvailable');
        return $pluginInfo ? $this->___callPlugins('isMultishippingCheckoutAvailable', func_get_args(), $pluginInfo) : parent::isMultishippingCheckoutAvailable();
    }
}
