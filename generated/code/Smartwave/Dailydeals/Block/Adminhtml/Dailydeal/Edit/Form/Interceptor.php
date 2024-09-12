<?php
namespace Smartwave\Dailydeals\Block\Adminhtml\Dailydeal\Edit\Form;

/**
 * Interceptor class for @see \Smartwave\Dailydeals\Block\Adminhtml\Dailydeal\Edit\Form
 */
class Interceptor extends \Smartwave\Dailydeals\Block\Adminhtml\Dailydeal\Edit\Form implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Backend\Block\Template\Context $context, \Magento\Framework\Registry $registry, \Magento\Framework\Data\FormFactory $formFactory, array $data = [])
    {
        $this->___init();
        parent::__construct($context, $registry, $formFactory, $data);
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
