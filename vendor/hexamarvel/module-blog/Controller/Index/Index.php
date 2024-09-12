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

class Index extends \Magento\Framework\App\Action\Action
{
    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var Helper
     */
    protected $helper;

    /**
     * Index constructor.
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
     * @return \Magento\Framework\App\ResponseInterface
     */
    public function execute()
    {
        $page = $this->resultPageFactory->create();
        if ($this->helper->getConfig('hexablog/blog_list/breadcrumbs_inlist')) {
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
                ]);
            }
        }

        if ($title = $this->helper->getConfig('hexablog/blog_list/blog_title')) {
            $page->getConfig()->getTitle()->set($title);
        }

        if ($metaKeyWords = $this->helper->getConfig('hexablog/blog_list/meta_keywords')) {
            $page->getConfig()->setKeywords($metaKeyWords);
        }

        if ($metaDescription = $this->helper->getConfig('hexablog/blog_list/meta_description')) {
            $page->getConfig()->setDescription($metaDescription);
        }

        return $page;
    }
}
