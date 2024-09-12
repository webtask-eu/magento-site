<?php
namespace Magento\SalesRule\Api\Data;

/**
 * Extension class for @see \Magento\SalesRule\Api\Data\RuleInterface
 */
class RuleExtension extends \Magento\Framework\Api\AbstractSimpleObject implements RuleExtensionInterface
{
    /**
     * @return \StripeIntegration\Payments\Api\Data\CouponInterface|null
     */
    public function getCoupon()
    {
        return $this->_get('coupon');
    }

    /**
     * @param \StripeIntegration\Payments\Api\Data\CouponInterface $coupon
     * @return $this
     */
    public function setCoupon(\StripeIntegration\Payments\Api\Data\CouponInterface $coupon)
    {
        $this->setData('coupon', $coupon);
        return $this;
    }
}
