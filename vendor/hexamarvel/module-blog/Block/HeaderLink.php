<?php
/**
 * @author Hexamarvel Team
 * @copyright Copyright (c) 2021 Hexamarvel (https://www.hexamarvel.com)
 * @package Hexamarvel_Blog
 */

namespace Hexamarvel\Blog\Block;

class HeaderLink extends \Magento\Framework\View\Element\Html\Link
{
    /**
     * Render block HTML.
     *
     * @return string
     */
    protected function _toHtml()
    {
        if (!$this->getIsEnabled() || empty($this->getLabel())) {
            return;
        }

        if (false != $this->getTemplate()) {
            return parent::_toHtml();
        }

        return '<li><a ' . $this->getLinkAttributes() . ' >' . $this->escapeHtml($this->getLabel()) . '</a></li>';
    }
}
