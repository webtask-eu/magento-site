<?php
/**
 * @author Hexamarvel Team
 * @copyright Copyright (c) 2021 Hexamarvel (https://www.hexamarvel.com)
 * @package Hexamarvel_Blog
 */
namespace Hexamarvel\Blog\Model\Config\Source;

class PostSorting implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => 'DESC', 'label' => __('Newest first')],
            ['value' => 'ASC', 'label' => __('Older first')]
        ];
    }
}
