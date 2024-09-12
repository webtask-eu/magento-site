<?php
namespace StripeIntegration\Payments\Commands\Cron\CleanupDBTables;

/**
 * Interceptor class for @see \StripeIntegration\Payments\Commands\Cron\CleanupDBTables
 */
class Interceptor extends \StripeIntegration\Payments\Commands\Cron\CleanupDBTables implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\StripeIntegration\Payments\Helper\AreaCodeFactory $areaCodeFactory, \StripeIntegration\Payments\Cron\CleanupDBTables $cleanupDBTables)
    {
        $this->___init();
        parent::__construct($areaCodeFactory, $cleanupDBTables);
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
