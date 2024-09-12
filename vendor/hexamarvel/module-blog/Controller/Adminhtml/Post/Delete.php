<?php
/**
 * @author Hexamarvel Team
 * @copyright Copyright (c) 2021 Hexamarvel (https://www.hexamarvel.com)
 * @package Hexamarvel_Blog
 */

namespace Hexamarvel\Blog\Controller\Adminhtml\Post;

class Delete extends \Magento\Backend\App\Action
{
    /**
     * @var \Hexamarvel\Blog\Model\PostFactory
     */
    private $postFactory;

    /**
     * @param \Magento\Backend\App\Action\Context
     * @param \Hexamarvel\Blog\Model\PostFactory $postFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Hexamarvel\Blog\Model\PostFactory $postFactory
    ) {
        parent::__construct($context);
        $this->postFactory = $postFactory;
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
                $model = $this->postFactory->create();
                $model->load($id);
                $model->delete();
                $this->messageManager->addSuccessMessage(__('You deleted the Post.'));
                return $resultRedirect->setPath('*/*/index');
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
                return $resultRedirect->setPath('*/*/edit', ['id' => $id]);
            }
        }
        $this->messageManager->addErrorMessage(__('We can\'t find a post to delete.'));
        return $resultRedirect->setPath('*/*/');
    }
}
