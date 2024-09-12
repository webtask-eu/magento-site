<?php
/**
 * @author Hexamarvel Team
 * @copyright Copyright (c) 2021 Hexamarvel (https://www.hexamarvel.com)
 * @package Hexamarvel_Blog
 */

namespace Hexamarvel\Blog\Ui\DataProvider;

use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Filesystem\DirectoryList;
use Hexamarvel\Blog\Model\ResourceModel\Post\CollectionFactory;

class PostForm extends \Magento\Ui\DataProvider\AbstractDataProvider
{
    /**
     * @var CollectionFactory
     */
    protected $collection;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var DirectoryList
     */
    protected $directoryList;

    /**
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param CollectionFactory $tabsFactory
     * @param StoreManagerInterface $storeManager
     * @param DirectoryList $directoryList
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $tabsFactory,
        StoreManagerInterface $storeManager,
        DirectoryList $directoryList,
        array $meta = [],
        array $data = []
    ) {
        $this->collection = $tabsFactory->create();
        $this->storeManager = $storeManager;
        $this->directoryList = $directoryList;
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
    }

    /**
     * @return array
     */
    public function getData()
    {
        $items = $this->collection->getItems();
        $data = [];
        foreach ($items as $post) {
            $postData = [
                'post_information' => [
                    'id' => $post->getId(),
                    'title' => $post->getTitle(),
                    'identifier' => $post->getIdentifier(),
                    'sort_order' => $post->getSortOrder(),
                    'store' => explode(',', $post->getStore()),
                    'category' => explode(',', $post->getCategory()),
                    'is_active' => $post->getIsActive(),
                    'short_content' => $post->getShortContent(),
                    'content' => $post->getContent(),
                    'thumbnail_image' => (($post->getThumbnailImage()!= null)?json_decode($post->getThumbnailImage()):''),
                    'blog_image' => (($post->getBlogImage()!=null)?json_decode($post->getBlogImage()):'')
                ],
                'advanced_options' => [
                    'meta_keywords' => $post->getMetaKeywords(),
                    'meta_description' => $post->getMetaDescription(),
                    'author' => $post->getAuthor(),
                    'created_on' => $post->getCreatedOn()
                ],
                'related_products' => $post->getRelatedProducts()
            ];

            $data[$post->getId()] = $postData;
        }

        if (!empty($data)) {
            return $data;
        }
    }
}
