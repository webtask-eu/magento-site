<?php
namespace Hexamarvel\Blog\Controller\Router;

/**
 * Interceptor class for @see \Hexamarvel\Blog\Controller\Router
 */
class Interceptor extends \Hexamarvel\Blog\Controller\Router implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Framework\App\ActionFactory $actionFactory, \Magento\Framework\Event\ManagerInterface $eventManager, \Hexamarvel\Blog\Helper\Data $helper, \Hexamarvel\Blog\Model\PostFactory $postFactory, \Hexamarvel\Blog\Model\CategoryFactory $categoryFactory)
    {
        $this->___init();
        parent::__construct($actionFactory, $eventManager, $helper, $postFactory, $categoryFactory);
    }

    /**
     * {@inheritdoc}
     */
    public function match(\Magento\Framework\App\RequestInterface $request)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'match');
        return $pluginInfo ? $this->___callPlugins('match', func_get_args(), $pluginInfo) : parent::match($request);
    }
}
