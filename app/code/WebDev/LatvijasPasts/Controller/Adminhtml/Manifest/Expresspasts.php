<?php

namespace WebDev\LatvijasPasts\Controller\Adminhtml\Manifest;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\View\Result\PageFactory;

class Expresspasts extends Action implements HttpGetActionInterface
{
    private PageFactory $resultPageFactory;

    public function __construct(Context $context, PageFactory $resultPageFactory)
    {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
    }

    /**
     * Show ExpressPasts Manifest Creation Form
     */
    public function execute()
    {
        $resultPage = $this->resultPageFactory->create();
        $resultPage->getConfig()->getTitle()->prepend(__('ExpressPasts Manifest'));
        return $resultPage;
    }
}
