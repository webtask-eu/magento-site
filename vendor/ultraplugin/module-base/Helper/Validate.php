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

namespace Ultraplugin\Base\Helper;

use Magento\Framework\App\CacheInterface;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\ProductMetadataInterface;
use Magento\Framework\Module\ModuleListInterface;

class Validate extends AbstractHelper
{
    /**
     * String
     */
    private const EXTENSION_VALIDATE_FREQUENCY = 86400 * 20;

    /**
     * String
     */
    private const CACHE_IDENTIFIER_VALIDATE_EXTENSION = 'ultraplugin_validate_extension_lastcheck';

    /**
     * @var ModuleListInterface
     */
    private $_moduleList;

    /**
     * @var CacheInterface
     */
    private $cacheManager;

    /**
     * @var ProductMetadataInterface
     */
    private $metadata;

    /**
     * Validate constructor.
     *
     * @param Context $context
     * @param ModuleListInterface $moduleList
     * @param CacheInterface $cacheManager
     * @param ProductMetadataInterface $metadata
     */
    public function __construct(
        Context $context,
        ModuleListInterface $moduleList,
        CacheInterface $cacheManager,
        ProductMetadataInterface $metadata
    ) {
        $this->_moduleList = $moduleList;
        $this->cacheManager = $cacheManager;
        $this->metadata = $metadata;
        parent::__construct($context);
    }

    /**
     * Check is allowed
     *
     * @return bool
     */
    public function checkIsAllowed()
    {
        $isAllowed = false;
        if (self::EXTENSION_VALIDATE_FREQUENCY + $this->getLastValidateTime() < time()) {
            $isAllowed = true;
        }
        return $isAllowed;
    }

    /**
     * Get last validate time
     *
     * @return string
     */
    public function getLastValidateTime()
    {
        return $this->cacheManager->load(self::CACHE_IDENTIFIER_VALIDATE_EXTENSION);
    }

    /**
     * Set last validate time
     *
     * @return $this
     */
    public function setLastValidateTime()
    {
        $this->cacheManager->save(time(), self::CACHE_IDENTIFIER_VALIDATE_EXTENSION);
        return $this;
    }
}
