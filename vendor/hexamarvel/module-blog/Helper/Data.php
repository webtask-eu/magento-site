<?php
/**
 * @author Hexamarvel Team
 * @copyright Copyright (c) 2021 Hexamarvel (https://www.hexamarvel.com)
 * @package Hexamarvel_Blog
 */

namespace Hexamarvel\Blog\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\ScopeInterface;

class Data extends AbstractHelper
{
    /**
     * @return bool
     */
    public function isEnabled()
    {
        return $this->getConfig('hexablog/general/enable');
    }

    /**
     * @return bool
     */
    public function isHeaderLinkEnabled()
    {
        return $this->getConfig('hexablog/general/enable') && $this->getConfig('hexablog/general/enable_header');
    }

    /**
     * @return string
     */
    public function getModuleRoute()
    {
        $route = $this->getConfig('hexablog/blog_list/route');
        return ($route) ? $route : 'blog';
    }

    /**
     * @return string
     */
    public function getHeaderLabel()
    {
        return $this->getConfig('hexablog/general/blog_menu_title');
    }

    /**
     * @return array
     */
    public function getPerPageArray()
    {
        $string = $this->getConfig('hexablog/blog_list/post_per_page');
        if ($string) {
            $stringArr = explode(',', $string);
            return array_combine($stringArr, $stringArr);
        }

        return [5 => 5, 10 => 10, 15 => 15, 20 => 20];
    }

    /**
     * @param string $path
     * @return string
     */
    public function getConfig($path)
    {
        return $this->scopeConfig->getValue($path, ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return string
     */
    public function getDefaultSortOrder()
    {
        $order = $this->getConfig('hexablog/blog_list/post_sorting');
        return ($order) ? $order : 'desc';
    }
}
