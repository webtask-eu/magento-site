<?php
/**
 * @author Hexamarvel Team
 * @copyright Copyright (c) 2021 Hexamarvel (https://www.hexamarvel.com)
 * @package Hexamarvel_Blog
 */

namespace Hexamarvel\Blog\Ui\Component\Listing\Column;

class CategoryOptions implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @var \Hexamarvel\Blog\Model\CategoryFactory
     */
    protected $categoryFactory;

    /**
     * @param \Hexamarvel\Blog\Model\CategoryFactory $categoryFactory
     */
    public function __construct(
        \Hexamarvel\Blog\Model\CategoryFactory $categoryFactory
    ) {
        $this->categoryFactory = $categoryFactory;
    }

    /**
     * @return array $options
     */
    public function toOptionArray()
    {
        $categoryCollection = $this->categoryFactory->create()->getCollection();
        $options = [];
        foreach ($categoryCollection as $key => $category) {
            $options[] = ['label' => $category->getCategoryTitle(), 'value' => $category->getCategoryIdentifier()];
        }
        return $options;
    }
}
