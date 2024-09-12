<?php
/**
 * @author Hexamarvel Team
 * @copyright Copyright (c) 2021 Hexamarvel (https://www.hexamarvel.com)
 * @package Hexamarvel_Blog
 */

namespace Hexamarvel\Blog\Controller\Adminhtml\Category;

class Edit extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var \Hexamarvel\Blog\Model\CategoryFactory
     */
    private $categoryFactory;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Hexamarvel\Blog\Model\CategoryFactory $categoryFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Hexamarvel\Blog\Model\CategoryFactory $categoryFactory
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->categoryFactory = $categoryFactory;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('id');
        $model = $this->categoryFactory->create();

        if ($id) {
            $model->load($id);
            if (!$model->getId()) {
                $this->messageManager->addErrorMessage(__('This category no longer exists.'));
                $resultRedirect = $this->resultRedirectFactory->create();
                return $resultRedirect->setPath('*/*/');
            }
        }

        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu(
            'Hexamarvel_Blog::view_cat'
        )->addBreadcrumb(__('Manage Categories'), __('Manage Categories'));
        $resultPage->addBreadcrumb(
            $id ? __('Edit Category') : __('New Category'),
            $id ? __('Edit Category') : __('New Category')
        );
        $resultPage->getConfig()->getTitle()->prepend(__('Categories'));
        $resultPage->getConfig()->getTitle()
            ->prepend($model->getId() ? $model->getCategoryTitle() : __('New Category'));
        return $resultPage;
    }
}
