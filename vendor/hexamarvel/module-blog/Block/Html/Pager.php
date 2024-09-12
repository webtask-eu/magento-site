<?php
/**
 * @author Hexamarvel Team
 * @copyright Copyright (c) 2021 Hexamarvel (https://www.hexamarvel.com)
 * @package Hexamarvel_Blog
 */

namespace Hexamarvel\Blog\Block\Html;

class Pager extends \Magento\Theme\Block\Html\Pager
{
    /**
     * Current template name
     *
     * @var string
     */
    protected $_template = 'Hexamarvel_Blog::html/pager.phtml';

    /**
     * @var \Hexamarvel\Blog\Helper\Data
     */
    protected $helper;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Hexamarvel\Blog\Helper\Data $helper
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Hexamarvel\Blog\Helper\Data $helper,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->helper      = $helper;
        $this->_storeManager = $storeManager;
        parent::__construct($context);
    }

    /**
     * @return string $order
     */
    public function getAvailableOrders()
    {
        return [
            'created_on' => __('Created At'),
            'author' => __('Added By'),
            'title'  => __('Post Name'),
            'sort_order'  => __('Sort Order')
        ];
    }

    /**
     * @return string $order
     */
    public function isOrderCurrent($key)
    {
        $orderBy = $this->getRequest()->getParam('order_by');
        if (empty($orderBy)) {
            $orderBy = 'created_on';
        }

        return $orderBy == $key ;
    }

    /**
     * @return string $order
     */
    public function getCurrentDirection()
    {
        $order = $this->getRequest()->getParam('order');
        if (empty($order)) {
            $order = $this->helper->getDefaultSortOrder();
        }

        return $order;
    }
}
