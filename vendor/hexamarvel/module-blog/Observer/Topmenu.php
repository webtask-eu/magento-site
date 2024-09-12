<?php
/**
 * @author Hexamarvel Team
 * @copyright Copyright (c) 2021 Hexamarvel (https://www.hexamarvel.com)
 * @package Hexamarvel_Blog
 */

namespace Hexamarvel\Blog\Observer;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Data\Tree\Node;
use Magento\Framework\Event\ObserverInterface;

class Topmenu implements ObserverInterface
{
    /**
     * @var \Hexamarvel\Blog\Helper\Data
     */
    protected $helper;

    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $urlBuilder;

    /**
     * @param \Hexamarvel\Blog\Helper\Data $helper
     * @param \Magento\Framework\UrlInterface $urlBuilder
     */
    public function __construct(
        \Hexamarvel\Blog\Helper\Data $helper,
        \Magento\Framework\UrlInterface $urlBuilder
    ) {
        $this->helper       = $helper;
        $this->urlBuilder   = $urlBuilder;
    }
    /**
     * @param EventObserver $observer
     * @return $this
     */
    public function execute(EventObserver $observer)
    {
        if (!$this->helper->isEnabled()) {
            return;
        }

        $menuTitle = $this->helper->getConfig('hexablog/general/blog_menu_title');
        $url = $this->helper->getModuleRoute();
        $canDisplay = $this->helper->getConfig('hexablog/general/enable_menu');
        if (empty($url) || empty($menuTitle) || !$canDisplay) {
            return;
        }

        $blogUrl = $this->urlBuilder->getUrl($url);
        /** @var \Magento\Framework\Data\Tree\Node $menu */
        $menu = $observer->getMenu();
        $tree = $menu->getTree();
        $data = [
            'name'      => $menuTitle,
            'id'        => 'blog-category-menu',
            'url'       => $blogUrl,
            'is_active' => false,
        ];
        $node = new Node($data, 'id', $tree, $menu);
        $menu->addChild($node);
        return $this;
    }
}
