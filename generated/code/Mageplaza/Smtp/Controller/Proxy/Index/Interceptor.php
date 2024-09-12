<?php
namespace Mageplaza\Smtp\Controller\Proxy\Index;

/**
 * Interceptor class for @see \Mageplaza\Smtp\Controller\Proxy\Index
 */
class Interceptor extends \Mageplaza\Smtp\Controller\Proxy\Index implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Framework\App\Action\Context $context, \Mageplaza\Smtp\Helper\EmailMarketing $helperEmailMarketing, \Magento\Framework\Controller\Result\RawFactory $resultRawFactory)
    {
        $this->___init();
        parent::__construct($context, $helperEmailMarketing, $resultRawFactory);
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
