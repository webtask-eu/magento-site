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

namespace Ultraplugin\Paymentfee\Model\Calculation\Calculator;

use Ultraplugin\Paymentfee\Helper\Data as FeeHelper;

abstract class AbstractCalculator implements CalculatorInterface
{
    /**
     * @var FeeHelper
     */
    protected $helper;

    /**
     * AbstractCalculation constructor.
     *
     * @param FeeHelper $helper
     */
    public function __construct(FeeHelper $helper)
    {
        $this->helper = $helper;
    }
}
