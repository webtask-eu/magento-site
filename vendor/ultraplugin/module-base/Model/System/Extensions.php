<?php
/**
 * UltraPlugin
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the ultraplugin.com license that is
 * available through the world-wide-web at this URL:
 * https://ultraplugin.com/end-user-license-agreement
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    UltraPlugin
 * @package     Ultraplugin_Base
 * @copyright   Copyright (c) UltraPlugin (https://ultraplugin.com/)
 * @license     https://ultraplugin.com/end-user-license-agreement
 */

namespace Ultraplugin\Base\Model\System;

use Magento\Backend\Block\Context;
use Magento\Backend\Model\Auth\Session;
use Magento\Config\Block\System\Config\Form\Fieldset;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Framework\View\Helper\Js;
use Ultraplugin\Base\Block\Adminhtml\Extensions as ExtensionsBlock;

class Extensions extends Fieldset
{
    /**
     * @var ExtensionsBlock
     */
    private $extensionBlock;

    /**
     * Extensions constructor.
     *
     * @param Context $context
     * @param Session $authSession
     * @param Js $jsHelper
     * @param ExtensionsBlock $extensionBlock
     * @param array $data
     */
    public function __construct(
        Context $context,
        Session $authSession,
        Js $jsHelper,
        ExtensionsBlock $extensionBlock,
        array $data = []
    ) {
        $this->extensionBlock = $extensionBlock;
        parent::__construct(
            $context,
            $authSession,
            $jsHelper,
            $data
        );
    }

    /**
     * Render fieldset html
     *
     * @param AbstractElement $element
     * @return string
     */
    public function render(AbstractElement $element)
    {
        return $this->extensionBlock->toHtml();
    }
}
