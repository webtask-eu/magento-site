<?php
namespace Mageplaza\LayeredNavigation\Model\Category\Attribute\Backend\Attributes;

/**
 * Interceptor class for @see \Mageplaza\LayeredNavigation\Model\Category\Attribute\Backend\Attributes
 */
class Interceptor extends \Mageplaza\LayeredNavigation\Model\Category\Attribute\Backend\Attributes implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct()
    {
        $this->___init();
    }

    /**
     * {@inheritdoc}
     */
    public function validate($object)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'validate');
        return $pluginInfo ? $this->___callPlugins('validate', func_get_args(), $pluginInfo) : parent::validate($object);
    }
}
