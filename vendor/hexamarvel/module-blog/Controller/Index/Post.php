<?php
/**
 * @author Hexamarvel Team
 * @copyright Copyright (c) 2021 Hexamarvel (https://www.hexamarvel.com)
 * @package Hexamarvel_Blog
 */

namespace Hexamarvel\Blog\Controller\Index;

use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Hexamarvel\Blog\Helper\Data as Helper;

class Post extends \Magento\Framework\App\Action\Action
{
    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var PageFactory
     */
    protected $helper;

    /**
     * Post constructor.
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param Helper $helper
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        Helper $helper
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->helper            = $helper;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $postTitle = $this->getRequest()->getParam('post_object')->getTitle();
        $page = $this->resultPageFactory->create();
        $page->getConfig()->getTitle()->set($postTitle);
        if ($this->helper->getConfig('hexablog/blog_view/breadcrumbs_inpost')) {
            $breadcrumbs = $page->getLayout()->getBlock('breadcrumbs');
            $breadcrumbs->addCrumb('home', [
                'label' => __('Home'),
                'title' => __('Home'),
                'link' => $this->_url->getUrl('')
            ]);
            if ($this->helper->getModuleRoute()) {
                $breadcrumbs->addCrumb('blog_link', [
                    'label' => ucfirst($this->helper->getModuleRoute()),
                    'title' => ucfirst($this->helper->getModuleRoute()),
                    'link' => $this->_url->getUrl($this->helper->getModuleRoute())
                ]);
            }

            $breadcrumbs->addCrumb('post_link', [
                'label' => $postTitle,
                'title' => $postTitle
            ]);
        }

        if ($metaKeyWords = $this->getRequest()->getParam('post_object')->getMetaKeywords()) {
            $page->getConfig()->setKeywords($metaKeyWords);
        }

        if ($metaDescription = $this->getRequest()->getParam('post_object')->getMetaDescription()) {
            $page->getConfig()->setDescription($metaDescription);
        }

        return $page;
    }
}
