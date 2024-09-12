<?php
/**
 * @author Hexamarvel Team
 * @copyright Copyright (c) 2021 Hexamarvel (https://www.hexamarvel.com)
 * @package Hexamarvel_Blog
 */
namespace Hexamarvel\Blog\Block;

class PostView extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Hexamarvel\Blog\Helper\Data
     */
    protected $helper;

    /**
     * @var \Hexamarvel\Blog\Model\PostFactory
     */
    protected $postFactory;

    /**
     * @var \Magento\Cms\Model\Template\FilterProvider
     */
    protected $contentProcessor;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    protected $timezoneInterface;

    /**
     * @var Magento\Framework\UrlInterface
     */
    protected $urlBuilder;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    protected $productCollectionFactory;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Hexamarvel\Blog\Helper\Data $helper
     * @param \Hexamarvel\Blog\Model\PostFactory $postFactory
     * @param \Magento\Cms\Model\Template\FilterProvider $contentProcessor
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezoneInterface
     * @param \Magento\Framework\UrlInterface $urlBuilder
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Hexamarvel\Blog\Helper\Data $helper,
        \Hexamarvel\Blog\Model\PostFactory $postFactory,
        \Magento\Cms\Model\Template\FilterProvider $contentProcessor,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezoneInterface,
        \Hexamarvel\Blog\Model\CategoryFactory $categoryFactory,
        \Magento\Framework\UrlInterface $urlBuilder,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Magento\Catalog\Block\Product\AbstractProduct $abstractBlock
    ) {
        $this->helper      = $helper;
        $this->postFactory = $postFactory;
        $this->contentProcessor = $contentProcessor;
        $this->timezoneInterface = $timezoneInterface;
        $this->categoryFactory = $categoryFactory;
        $this->urlBuilder = $urlBuilder;
        $this->storeManager = $storeManager;
        $this->_productCollectionFactory = $productCollectionFactory;
        $this->abstractBlock = $abstractBlock;
        parent::__construct($context);
    }

    /**
     * @return obj post
     */
    public function getPageLayout()
    {
        return $this->helper->getConfig('hexablog/blog_view/page_layout');
    }

    /**
     * @return obj post
     */
    public function getPost()
    {
        return $this->getRequest()->getParam('post_object');
    }

    /**
     * @param json $imageJson
     * @return string imageUrl
     */
    public function getImage($imageJson)
    {
        $imageArray = json_decode($imageJson, true);
        return str_replace('tmp/images', 'post/images', $imageArray[0]['url']);
    }

    /**
     * @param
     * @return
     */
    public function getContent($value)
    {
        return $this->contentProcessor->getPageFilter()->filter($value);
    }

    /**
     * @param obj $post
     * @return string time
     */
    public function getCreatedDate($post)
    {
        $dateFormat = $this->helper->getConfig('hexablog/general/date_format');
        return $this->timezoneInterface->date(new \DateTime($post->getCreatedOn()))
                    ->format(($dateFormat) ? $dateFormat : 'm/d/y H:i:s');
    }

    /**
     * @param obj $post
     * @return string time
     */
    public function getCategories($categories)
    {
        $category = '';

        foreach (explode(',', $categories) as $key => $value) {
            $categoryObj = $this->categoryFactory->create()->load(
                $value,
                'category_identifier'
            );
            if ($categoryObj->getIsActive()) {
                $category .= $categoryObj->getCategoryTitle() .', ';
            }
        }

        return rtrim($category, ', ');
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
    public function getSidebarCategories()
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
     * @param obj $post
     * @return string url
     */
    public function getPostUrl($post)
    {
        $route = $this->helper->getModuleRoute() . '/post/' . $post->getIdentifier();
        return $this->urlBuilder->getUrl($route);
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

    /**
     * @param obj $post
     * @return obj $collection
     */
    public function getRelatedProducts($post)
    {
        $filterIds = [];
        if ($post->getRelatedProducts()) {
            foreach (json_decode($post->getRelatedProducts(), true) as $key => $value) {
                $filterIds[] = $key;
            }
        }

        $collection = $this->_productCollectionFactory->create();
        $collection->addAttributeToSelect('*');
        $collection->addFieldToFilter('entity_id', ['in' => $filterIds]);

        return $collection;
    }

    /**
     * @param obj $_item
     * @return string product url
     */
    public function getProductUrl($_item)
    {
        return $this->abstractBlock->getProductUrl($_item);
    }

    /**
     * @param obj $_item
     * @param string $image
     * @return string image url
     */
    public function getProductImage($_item, $image)
    {
        return $this->abstractBlock->getImage($_item, $image);
    }

    /**
     * @param obj $_item
     * @return string price
     */
    public function getProductPrice($_item)
    {
        return $this->abstractBlock->getProductPrice($_item);
    }

    /**
     * @param obj $_item
     * @return string addtocart url
     */
    public function getAddToCartUrl($_item)
    {
        return $this->abstractBlock->getAddToCartUrl($_item);
    }
}
