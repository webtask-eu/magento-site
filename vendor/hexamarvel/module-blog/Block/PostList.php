<?php
/**
 * @author Hexamarvel Team
 * @copyright Copyright (c) 2021 Hexamarvel (https://www.hexamarvel.com)
 * @package Hexamarvel_Blog
 */

namespace Hexamarvel\Blog\Block;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

class PostList extends \Magento\Framework\View\Element\Template
{
    /**
     * @var Hexamarvel\Blog\Helper\Data
     */
    protected $helper;

    /**
     * @var \Hexamarvel\Blog\Model\PostFactory
     */
    protected $postFactory;

    /**
     * @var \Hexamarvel\Blog\Model\CategoryFactory
     */
    protected $categoryFactory;

    /**
     * @var Magento\Framework\UrlInterface
     */
    protected $urlBuilder;

    /**
     * @var Magento\Cms\Model\Template\FilterProvider
     */
    protected $contentProcessor;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Hexamarvel\Blog\Helper\Data $helper
     * @param \Hexamarvel\Blog\Model\PostFactory $postFactory
     * @param \Hexamarvel\Blog\Model\CategoryFactory $categoryFactory,
     * @param \Magento\Framework\UrlInterface $urlBuilder
     * @param \Magento\Cms\Model\Template\FilterProvider $contentProcessor
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Hexamarvel\Blog\Helper\Data $helper,
        \Hexamarvel\Blog\Model\PostFactory $postFactory,
        \Hexamarvel\Blog\Model\CategoryFactory $categoryFactory,
        \Magento\Framework\UrlInterface $urlBuilder,
        \Magento\Cms\Model\Template\FilterProvider $contentProcessor,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->helper      = $helper;
        $this->postFactory = $postFactory;
        $this->categoryFactory = $categoryFactory;
        $this->urlBuilder = $urlBuilder;
        $this->contentProcessor = $contentProcessor;
        $this->storeManager = $storeManager;
        parent::__construct($context);
    }

    /**
     * @return object postCollection
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        if ($this->getPostCollection()) {
            $pager = $this->getLayout()->createBlock(
                'Hexamarvel\Blog\Block\Html\Pager',
                'custom.history.pager'
            )->setAvailableLimit(
                $this->helper->getPerPageArray()
            )->setShowPerPage(true)->setCollection(
                $this->getPostCollection()
            );
            $this->setChild('pager', $pager);
            $this->getPostCollection()->load();
        }

        return $this;
    }

    /**
     * @return obj
     */
    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }

    /**
     * @return obj $collection
     */
    public function getPostCollection()
    {
        $limit = $this->helper->getPerPageArray();
        $arrayKeys = array_keys($limit);

        $page = ($this->getRequest()->getParam('p')) ? $this->getRequest()->getParam('p') : 1;
        $pageSize = ($this->getRequest()->getParam('limit')) ? $this->getRequest()->getParam('limit') : $limit[$arrayKeys[0]];
        $collection = $this->postFactory->create()->getCollection();
        $collection->setPageSize($pageSize);
        $collection->setCurPage($page);
        $collection->setOrder(
            $this->getSortBy(),
            $this->getSortDirection()
        );
        $collection->addFieldToFilter(
            'store',
            [
                ['eq' => 0],
                ['finset' => $this->storeManager->getStore()->getId()]
            ]
        );
        $collection->addFieldToFilter(
            'is_active',
            '1'
        );
        return $collection;
    }

    /**
     * @param json $thumbnailJson
     * @return string url
     */
    public function getThumnailImage($thumbnailJson)
    {
        $imageArray = json_decode($thumbnailJson, true);
        return $imageArray[0]['url'];
    }

    /**
     * @param obj $post
     * @return string url
     */
    public function getPostUrl($post)
    {
        $route = $this->helper->getModuleRoute() . '/post/' . $post->getIdentifier();
        return $this->urlBuilder->getUrl($route);
    }

    /**
     * @param string $value
     * @return string value
     */
    public function getContent($value)
    {
        return $this->contentProcessor->getPageFilter()->filter($value);
    }

    /**
     * @return string sortingValue
     */
    public function getSortBy()
    {
        $direction = $this->getRequest()->getParam('order_by');
        if (empty($direction)) {
            $direction = 'created_on';
        }

        return $direction;
    }

    /**
     * @return string sortingValue
     */
    public function getSortDirection()
    {
        $direction = $this->getRequest()->getParam('order');
        if (empty($direction)) {
            $direction = $this->helper->getDefaultSortOrder();
        }

        return $direction;
    }

    /**
     * @return obj post
     */
    public function getPageLayout()
    {
        return $this->helper->getConfig('hexablog/blog_list/page_layout');
    }

    /**
     * @return obj $collection
     */
    public function getRecentBlogs()
    {
        $pageSize = ($this->helper->getConfig('hexablog/general/recent_blog')) ?
        $this->helper->getConfig('hexablog/general/recent_blog') : '0';

        $collection = $this->postFactory->create()->getCollection();
        if ($pageSize) {
            $collection->setPageSize($pageSize);
            $collection->setCurPage(1);
            $collection->addFieldToFilter(
                'store',
                [
                    ['eq' => 0],
                    ['finset' => $this->storeManager->getStore()->getId()]
                ]
            );
            $collection->setOrder(
                'created_on',
                'desc'
            );
            $collection->addFieldToFilter(
                'is_active',
                '1'
            );
        } else {
            $collection->addFieldToFilter(
                'id',
                '0'
            );
        }

        return $collection;
    }

    /**
     * @return obj $collection
     */
    public function getCategories()
    {
        $pageSize = ($this->helper->getConfig('hexablog/general/sidebar_category')) ?
        $this->helper->getConfig('hexablog/general/sidebar_category') : '0';
        $collection = $this->categoryFactory->create()->getCollection();
        if ($pageSize) {
            $collection->setPageSize($pageSize)->setCurPage(1);
            $collection->addFieldToFilter(
                'is_active',
                '1'
            );

            return $collection;
        }

        $collection->addFieldToFilter(
            'id',
            '0'
        );
        return $collection;
    }

    /**
     * @param obj $category
     * @return string url
     */
    public function getCategoryUrl($category)
    {
        $route = $this->helper->getModuleRoute() . '/category/' . $category->getCategoryIdentifier();
        return $this->urlBuilder->getUrl($route);
    }
}
