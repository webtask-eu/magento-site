<?php
/**
 * @author Hexamarvel Team
 * @copyright Copyright (c) 2021 Hexamarvel (https://www.hexamarvel.com)
 * @package Hexamarvel_Blog
 */

namespace Hexamarvel\Blog\Controller\Adminhtml\Post;

class Index extends \Magento\Backend\App\Action
{
    const ADMIN_RESOURCE = 'Hexamarvel_Blog::view_post';

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory = false;

    /**
     * @param \Magento\Backend\App\Action\Context $context,
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
    }

    /**
     * @return obj $resultPage
     */
    public function execute()
    {
        $resultPage = $this->resultPageFactory->create();

        $resultPage->setActiveMenu('Hexamarvel_Blog::view_post');
        $resultPage->addBreadcrumb(__('Manage Posts'), __('Manage Posts'));
        $resultPage->getConfig()->getTitle()->prepend(__('Posts'));

        return $resultPage;
    }
}
