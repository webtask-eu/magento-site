<?php
namespace StripeIntegration\Payments\Commands\Subscriptions\MigrateSubscriptionPriceCommand;

/**
 * Interceptor class for @see \StripeIntegration\Payments\Commands\Subscriptions\MigrateSubscriptionPriceCommand
 */
class Interceptor extends \StripeIntegration\Payments\Commands\Subscriptions\MigrateSubscriptionPriceCommand implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Store\Model\StoreManagerInterface $storeManager, \Magento\Framework\App\ResourceConnection $resource)
    {
        $this->___init();
        parent::__construct($storeManager, $resource);
    }

    /**
     * {@inheritdoc}
     */
    public function run(\Symfony\Component\Console\Input\InputInterface $input, \Symfony\Component\Console\Output\OutputInterface $output) : int
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'run');
        return $pluginInfo ? $this->___callPlugins('run', func_get_args(), $pluginInfo) : parent::run($input, $output);
    }
}
