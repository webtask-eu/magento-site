<?php
namespace Magento\SalesRule\Api\Data;

/**
 * ExtensionInterface class for @see \Magento\SalesRule\Api\Data\RuleInterface
 */
interface RuleExtensionInterface extends \Magento\Framework\Api\ExtensionAttributesInterface
{
    /**
     * @return \StripeIntegration\Payments\Api\Data\CouponInterface|null
     */
    public function getCoupon();

    /**
     * @param \StripeIntegration\Payments\Api\Data\CouponInterface $coupon
     * @return $this
     */
    public function setCoupon(\StripeIntegration\Payments\Api\Data\CouponInterface $coupon);
}
