<?php
/**
 * @author Hexamarvel Team
 * @copyright Copyright (c) 2021 Hexamarvel (https://www.hexamarvel.com)
 * @package hexamarvel_blog
 */
namespace Hexamarvel\Blog\Model\ResourceModel\Category;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\Abstractcollection
{
    /**
     * @var primaryId
     */
    protected $_idFieldName = 'id';

    public function _construct()
    {
        $this->_init(
            \Hexamarvel\Blog\Model\Category::class,
            \Hexamarvel\Blog\Model\ResourceModel\Category::class
        );
    }
}
