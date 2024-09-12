<?php
/**
 * @author Hexamarvel Team
 * @copyright Copyright (c) 2021 Hexamarvel (https://www.hexamarvel.com)
 * @package Hexamarvel_Blog
 */
namespace Hexamarvel\Blog\Model\ResourceModel;

class Category extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    public function _construct()
    {
        $this->_init('hexamarvel_category', 'id');
    }
}
