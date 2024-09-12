<?php
namespace StripeIntegration\Payments\Commands\Subscriptions\CreateFromOrderCommand;

/**
 * Interceptor class for @see \StripeIntegration\Payments\Commands\Subscriptions\CreateFromOrderCommand
 */
class Interceptor extends \StripeIntegration\Payments\Commands\Subscriptions\CreateFromOrderCommand implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Store\Model\StoreManagerInterface $storeManager, \Magento\Framework\App\ResourceConnection $resource, \Magento\Sales\Api\OrderRepositoryInterface $orderRepository, \Magento\Sales\Api\Data\OrderInterfaceFactory $orderFactory)
    {
        $this->___init();
        parent::__construct($storeManager, $resource, $orderRepository, $orderFactory);
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
