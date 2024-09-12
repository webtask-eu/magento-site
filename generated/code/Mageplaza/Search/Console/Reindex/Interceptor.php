<?php
namespace Mageplaza\Search\Console\Reindex;

/**
 * Interceptor class for @see \Mageplaza\Search\Console\Reindex
 */
class Interceptor extends \Mageplaza\Search\Console\Reindex implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Framework\App\State $appState)
    {
        $this->___init();
        parent::__construct($appState);
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
