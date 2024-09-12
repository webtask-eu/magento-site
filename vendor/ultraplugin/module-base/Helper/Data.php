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

use Magento\Backend\App\Area\FrontNameResolver;
use Magento\Framework\App\State;
use Magento\Framework\Module\Manager as ModuleManager;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Pricing\Helper\Data as PriceHelper;
use Magento\Framework\Serialize\Serializer\Json;

class Data
{
    /**
     * @var ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * @var ModuleManager
     */
    protected $moduleManager;

    /**
     * @var State
     */
    protected $state;

    /**
     * @var PriceHelper
     */
    protected $priceHelper;

    /**
     * @var Json
     */
    protected $serialize;

    /**
     * Data constructor.
     *
     * @param ObjectManagerInterface $objectManager
     * @param ModuleManager $moduleManager
     * @param State $state
     * @param PriceHelper $priceHelper
     * @param Json $serialize
     */
    public function __construct(
        ObjectManagerInterface $objectManager,
        ModuleManager $moduleManager,
        State $state,
        PriceHelper $priceHelper,
        Json $serialize
    ) {
        $this->objectManager = $objectManager;
        $this->moduleManager = $moduleManager;
        $this->state = $state;
        $this->priceHelper = $priceHelper;
        $this->serialize = $serialize;
    }

    /**
     * Create object
     *
     * @param mixed $path
     * @param array $arguments
     * @return mixed
     */
    public function createObject($path, $arguments = [])
    {
        return $this->objectManager->create($path, $arguments);
    }

    /**
     * Get object
     *
     * @param mixed $path
     * @return mixed
     */
    public function getObject($path)
    {
        return $this->objectManager->get($path);
    }

    /**
     * Check whether or not the module output is enabled in Configuration
     *
     * @param string $moduleName Full module name
     * @return boolean
     */
    public function isModuleOutputEnabled($moduleName)
    {
        return $this->moduleManager->isOutputEnabled($moduleName);
    }

    /**
     * Check is block data
     *
     * @param string $data
     * @return bool
     */
    public function checkBlockData($data)
    {
        if ($data == '1') {
            return true;
        } elseif ($data == '0') {
            return false;
        }
    }

    /**
     * Check is backend area
     *
     * @return bool
     */
    public function isBackendArea()
    {
        try {
            $isBackend = false;
            $areaCode = $this->state->getAreaCode();
            if ($areaCode == FrontNameResolver::AREA_CODE) {
                $isBackend = true;
            }
        } catch (\Exception $e) {
            $isBackend = false;
        }
        return $isBackend;
    }

    /**
     * Get price helper
     *
     * @return PriceHelper
     */
    public function getPriceHelper()
    {
        return $this->priceHelper;
    }

    /**
     * Unserialize data
     *
     * @param mixed $data
     * @return array|bool|float|int|mixed|string|null
     */
    public function unserialize($data)
    {
        return $this->serialize->unserialize($data);
    }

    /**
     * Serialize data
     *
     * @param mixed $data
     * @return bool|string
     */
    public function serialize($data)
    {
        return $this->serialize->serialize($data);
    }
}
