<?php
namespace Magento\PageBuilder\Model\Config;

/**
 * Interceptor class for @see \Magento\PageBuilder\Model\Config
 */
class Interceptor extends \Magento\PageBuilder\Model\Config implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\PageBuilder\Model\Config\CompositeReader $reader, \Magento\Framework\Config\CacheInterface $cache, \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig, $cacheId = 'pagebuilder_config')
    {
        $this->___init();
        parent::__construct($reader, $cache, $scopeConfig, $cacheId);
    }

    /**
     * {@inheritdoc}
     */
    public function isEnabled() : bool
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'isEnabled');
        return $pluginInfo ? $this->___callPlugins('isEnabled', func_get_args(), $pluginInfo) : parent::isEnabled();
    }
}
