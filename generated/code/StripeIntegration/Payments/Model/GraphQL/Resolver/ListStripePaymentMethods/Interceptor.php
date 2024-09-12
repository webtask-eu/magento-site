<?php
namespace StripeIntegration\Payments\Model\GraphQL\Resolver\ListStripePaymentMethods;

/**
 * Interceptor class for @see \StripeIntegration\Payments\Model\GraphQL\Resolver\ListStripePaymentMethods
 */
class Interceptor extends \StripeIntegration\Payments\Model\GraphQL\Resolver\ListStripePaymentMethods implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\StripeIntegration\Payments\Api\Service $api, \Magento\Framework\Serialize\SerializerInterface $serializer)
    {
        $this->___init();
        parent::__construct($api, $serializer);
    }

    /**
     * {@inheritdoc}
     */
    public function resolve(\Magento\Framework\GraphQl\Config\Element\Field $field, $context, \Magento\Framework\GraphQl\Schema\Type\ResolveInfo $info, ?array $value = null, ?array $args = null)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'resolve');
        return $pluginInfo ? $this->___callPlugins('resolve', func_get_args(), $pluginInfo) : parent::resolve($field, $context, $info, $value, $args);
    }
}
