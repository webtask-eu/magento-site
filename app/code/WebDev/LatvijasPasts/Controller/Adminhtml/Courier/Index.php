<?php

namespace WebDev\LatvijasPasts\Controller\Adminhtml\Courier;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\View\Result\PageFactory;

class Index extends Action implements HttpGetActionInterface
{
    private PageFactory $resultPageFactory;

    public function __construct(Context $context, PageFactory $resultPageFactory)
    {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
    }

    /**
     * Show Courier Call Creation Form
     */
    public function execute()
    {
        $resultPage = $this->resultPageFactory->create();
        $resultPage->getConfig()->getTitle()->prepend(__('LatvijasPasts Courier'));
        return $resultPage;
    }
}
