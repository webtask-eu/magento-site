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

namespace Ultraplugin\Base\Block\Adminhtml;

use Magento\Framework\Filesystem\Driver\File;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Framework\View\Element\Template;

class Extensions extends Template
{
    private const ULTRAPLUGIN_EXTENSION_JSON_URL = 'https://ultraplugin.com/media/modules/modules.json';

    /**
     * @var string
     */
    protected $_template = 'Ultraplugin_Base::extensions.phtml';

    /**
     * @var PriceCurrencyInterface
     */
    private $priceCurrency;

    /**
     * @var File
     */
    private $driverFile;

    /**
     * Extensions constructor.
     *
     * @param Template\Context $context
     * @param PriceCurrencyInterface $priceCurrency
     * @param File $driverFile
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        PriceCurrencyInterface $priceCurrency,
        File $driverFile,
        array $data = []
    ) {
        $this->priceCurrency = $priceCurrency;
        $this->driverFile = $driverFile;
        parent::__construct($context, $data);
    }

    /**
     * Get module data
     *
     * @return string
     */
    public function getModuleData()
    {
        try {
            $moduleData = '';
            $fileGet = self::ULTRAPLUGIN_EXTENSION_JSON_URL;
            $data = $this->driverFile->fileGetContents($fileGet);
            if ($data) {
                $moduleData = json_decode($data);
            }
        } catch (\Exception $e) {
            $moduleData = '';
        }

        return $moduleData;
    }

    /**
     * Get price
     *
     * @param float $price
     * @return string
     */
    public function getFormattedPrice($price)
    {
        return $this->priceCurrency->convertAndFormat($price, false, 0);
    }
}
