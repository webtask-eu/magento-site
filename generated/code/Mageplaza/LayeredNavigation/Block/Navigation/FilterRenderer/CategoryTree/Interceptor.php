<?php
namespace Mageplaza\LayeredNavigation\Block\Navigation\FilterRenderer\CategoryTree;

/**
 * Interceptor class for @see \Mageplaza\LayeredNavigation\Block\Navigation\FilterRenderer\CategoryTree
 */
class Interceptor extends \Mageplaza\LayeredNavigation\Block\Navigation\FilterRenderer\CategoryTree implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Framework\View\Element\Template\Context $context, \Mageplaza\LayeredNavigation\Helper\Data $helperData, array $data = [])
    {
        $this->___init();
        parent::__construct($context, $helperData, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function render(\Magento\Catalog\Model\Layer\Filter\FilterInterface $filter)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'render');
        return $pluginInfo ? $this->___callPlugins('render', func_get_args(), $pluginInfo) : parent::render($filter);
    }
}
