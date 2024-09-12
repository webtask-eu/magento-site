<?php
/**
 * @author Hexamarvel Team
 * @copyright Copyright (c) 2021 Hexamarvel (https://www.hexamarvel.com)
 * @package Hexamarvel_Blog
 */
namespace Hexamarvel\Blog\Model\Config\Source;

class PageLayout implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => 'single-column', 'label' => __('1 Column')],
            ['value' => 'column-left', 'label' => __('2 Column Left')],
            ['value' => 'column-right', 'label' => __('2 Column Right')]
        ];
    }
}
