<?php
namespace Mageplaza\Smtp\Block\Adminhtml\AbandonedCart\Edit\Form;

/**
 * Interceptor class for @see \Mageplaza\Smtp\Block\Adminhtml\AbandonedCart\Edit\Form
 */
class Interceptor extends \Mageplaza\Smtp\Block\Adminhtml\AbandonedCart\Edit\Form implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Backend\Block\Template\Context $context, \Magento\Framework\Registry $registry, \Magento\Framework\Data\FormFactory $formFactory, \Magento\Customer\Model\Address\Config $addressConfig, \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency, \Magento\Config\Model\Config\Source\Email\Identity $emailIdentity, \Magento\Config\Model\Config\Source\Email\Template $emailTemplate, \Magento\Tax\Model\Config $taxConfig, \Mageplaza\Smtp\Model\ResourceModel\Log\CollectionFactory $logCollectionFactory, \Mageplaza\Smtp\Helper\EmailMarketing $helperEmailMarketing, \Magento\Customer\Api\GroupRepositoryInterface $groupRepository, array $data = [])
    {
        $this->___init();
        parent::__construct($context, $registry, $formFactory, $addressConfig, $priceCurrency, $emailIdentity, $emailTemplate, $taxConfig, $logCollectionFactory, $helperEmailMarketing, $groupRepository, $data);
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
