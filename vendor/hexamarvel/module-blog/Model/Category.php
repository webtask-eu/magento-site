<?php
/**
 * @author Hexamarvel Team
 * @copyright Copyright (c) 2021 Hexamarvel (https://www.hexamarvel.com)
 * @package Hexamarvel_Blog
 */
namespace Hexamarvel\Blog\Model;

class Category extends \Magento\Framework\Model\AbstractModel
{
    public function _construct()
    {
        $this->_init(\Hexamarvel\Blog\Model\ResourceModel\Category::class);
    }
}
