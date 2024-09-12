<?php
namespace Mageplaza\LayeredNavigation\Block\Adminhtml\Product\Attribute\Edit\Tab\Layer;

/**
 * Interceptor class for @see \Mageplaza\LayeredNavigation\Block\Adminhtml\Product\Attribute\Edit\Tab\Layer
 */
class Interceptor extends \Mageplaza\LayeredNavigation\Block\Adminhtml\Product\Attribute\Edit\Tab\Layer implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Backend\Block\Template\Context $context, \Magento\Framework\Registry $registry, \Magento\Framework\Data\FormFactory $formFactory, \Mageplaza\LayeredNavigation\Helper\Data $helperData, \Mageplaza\LayeredNavigation\Model\Category\Attribute\Source\RenderCategoryTree $renderCategoryTree, \Mageplaza\LayeredNavigation\Model\Category\Attribute\Source\CategoriesLevel $categoriesLevel, \Mageplaza\LayeredNavigation\Model\Category\Attribute\Source\ExpandSubcategories $expandSubcategories, \Magento\Config\Model\Config\Structure\Element\Dependency\FieldFactory $fieldFactory, array $data = [])
    {
        $this->___init();
        parent::__construct($context, $registry, $formFactory, $helperData, $renderCategoryTree, $categoriesLevel, $expandSubcategories, $fieldFactory, $data);
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
