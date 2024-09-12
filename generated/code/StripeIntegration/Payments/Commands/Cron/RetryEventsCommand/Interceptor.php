<?php
namespace StripeIntegration\Payments\Commands\Cron\RetryEventsCommand;

/**
 * Interceptor class for @see \StripeIntegration\Payments\Commands\Cron\RetryEventsCommand
 */
class Interceptor extends \StripeIntegration\Payments\Commands\Cron\RetryEventsCommand implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\StripeIntegration\Payments\Helper\AreaCodeFactory $areaCodeFactory, \StripeIntegration\Payments\Model\ResourceModel\WebhookEvent\CollectionFactory $webhookEventCollectionFactory)
    {
        $this->___init();
        parent::__construct($areaCodeFactory, $webhookEventCollectionFactory);
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
