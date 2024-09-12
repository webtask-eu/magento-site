<?php
/**
 * @author Hexamarvel Team
 * @copyright Copyright (c) 2021 Hexamarvel (https://www.hexamarvel.com)
 * @package Hexamarvel_Blog
 */

namespace Hexamarvel\Blog\Ui\DataProvider;

use Hexamarvel\Blog\Model\ResourceModel\Category\CollectionFactory;

class CategoryForm extends \Magento\Ui\DataProvider\AbstractDataProvider
{
    /**
     * @var CollectionFactory
     */
    protected $collection;

    /**
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param CollectionFactory $categoryFactory
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $categoryFactory,
        array $meta = [],
        array $data = []
    ) {
        $this->collection = $categoryFactory->create();
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
    }

    /**
     * @return array
     */
    public function getData()
    {
        $items = $this->collection->getItems();
        $data = [];
        foreach ($items as $category) {
            $data[$category->getId()]['category_information'] = $category->getData();
        }

        if (!empty($data)) {
            return $data;
        }
    }
}
