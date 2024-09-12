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
 * @package     Ultraplugin_Paymentfee
 * @copyright   Copyright (c) UltraPlugin (https://ultraplugin.com/)
 * @license     https://ultraplugin.com/end-user-license-agreement
 */

namespace Ultraplugin\Paymentfee\Model;

use Magento\Checkout\Model\ConfigProviderInterface;
use Ultraplugin\Paymentfee\Helper\Data;

class PaymentFeeConfigProvider implements ConfigProviderInterface
{
    /**
     * @var Data
     */
    protected $helper;

    /**
     * PaymentFeeConfigProvider constructor.
     *
     * @param Data $helper
     */
    public function __construct(Data $helper)
    {
        $this->helper = $helper;
    }

    /**
     * Get payment fee config
     *
     * @return array
     */
    public function getConfig()
    {
        $displayExclTax = $this->helper->displayExclTax();
        $displayInclTax = $this->helper->displayInclTax();

        $isDescription = $this->helper->isDescription();
        $description = $this->helper->getDescription();

        $paymentFeeConfig = [
            'ultraplugin_paymentfee' => [
                'isEnabled' => $this->helper->isEnable(),
                'title' => $this->helper->getTitle(),
                'description' => $isDescription ? $description : false,
                'isTaxEnabled' => $this->helper->isTaxEnabled(),
                'displayBoth' => ($displayExclTax && $displayInclTax),
                'displayInclTax' => $this->helper->displayInclTax(),
                'displayExclTax' => $this->helper->displayExclTax()
            ]
        ];

        return $paymentFeeConfig;
    }
}
