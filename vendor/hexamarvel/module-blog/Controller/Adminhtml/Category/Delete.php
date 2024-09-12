<?php
/**
 * @author Hexamarvel Team
 * @copyright Copyright (c) 2021 Hexamarvel (https://www.hexamarvel.com)
 * @package Hexamarvel_Blog
 */

namespace Hexamarvel\Blog\Controller\Adminhtml\Category;

class Delete extends \Magento\Backend\App\Action
{
    /**
     * @var \Hexamarvel\Blog\Model\CategoryFactory
     */
    private $categoryFactory;

    /**
     * @param \Magento\Backend\App\Action\Context
     * @param \Hexamarvel\Blog\Model\CategoryFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Hexamarvel\Blog\Model\CategoryFactory $categoryFactory
    ) {
        parent::__construct($context);
        $this->categoryFactory = $categoryFactory;
    }

    /**
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $id = $this->getRequest()->getParam('id');
        if ($id) {
            try {
                $model = $this->categoryFactory->create();
                $model->load($id);
                $model->delete();
                $this->messageManager->addSuccessMessage(__('You deleted the Category.'));
                return $resultRedirect->setPath('*/*/index');
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
                return $resultRedirect->setPath('*/*/edit', ['id' => $id]);
            }
        }
        $this->messageManager->addErrorMessage(__('We can\'t find a category to delete.'));
        return $resultRedirect->setPath('*/*/');
    }
}
