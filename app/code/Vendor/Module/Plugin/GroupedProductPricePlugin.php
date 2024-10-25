<?php
namespace Vendor\Module\Plugin;

use Magento\Catalog\Block\Product\AbstractProduct;
use Magento\Catalog\Model\Product;
use Magento\Framework\Pricing\Helper\Data as PriceHelper;

class GroupedProductPricePlugin
{
    protected $priceHelper;

    public function __construct(PriceHelper $priceHelper)
    {
        $this->priceHelper = $priceHelper;
    }

    public function aroundGetProductPriceHtml(
        AbstractProduct $subject,
        callable $proceed,
        Product $product
    ) {
        if ($product->getTypeId() === \Magento\GroupedProduct\Model\Product\Type\Grouped::TYPE_CODE) {
            $totalPrice = 0;
            $associatedProducts = $product->getTypeInstance()->getAssociatedProducts($product);

            foreach ($associatedProducts as $item) {
                $totalPrice += $item->getFinalPrice();
            }

            return '<span class="price">' . $this->priceHelper->currency($totalPrice, true, false) . '</span>';
        }

        return $proceed($product);
    }
}
