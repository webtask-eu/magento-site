<?php
/**
 * @author Hexamarvel Team
 * @copyright Copyright (c) 2021 Hexamarvel (https://www.hexamarvel.com)
 * @package Hexamarvel_Blog
 */

namespace Hexamarvel\Blog\Controller;

use Magento\Framework\App\ActionFactory;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\RouterInterface;
use Magento\Framework\DataObject;
use Magento\Framework\Event\ManagerInterface as EventManagerInterface;
use Hexamarvel\Blog\Helper\Data as CustomRouteHelper;
use Hexamarvel\Blog\Model\PostFactory;
use Hexamarvel\Blog\Model\CategoryFactory;

class Router implements RouterInterface
{
    /**
     * @var bool
     */
    private $dispatched = false;

    /**
     * @var ActionFactory
     */
    protected $actionFactory;

    /**
     * @var EventManagerInterface
     */
    protected $eventManager;

    /**
     * @var CustomRouteHelper
     */
    protected $helper;

    /**
     * @var PostFactory
     */
    protected $postFactory;

    /**
     * @var CategoryFactory
     */
    protected $categoryFactory;

    /**
     * Router constructor.
     *
     * @param ActionFactory $actionFactory
     * @param EventManagerInterface $eventManager
     * @param CustomRouteHelper $helper
     * @param PostFactory $postFactory
     * @param CategoryFactory $categoryFactory
     */
    public function __construct(
        ActionFactory $actionFactory,
        EventManagerInterface $eventManager,
        CustomRouteHelper $helper,
        PostFactory $postFactory,
        CategoryFactory $categoryFactory
    ) {
        $this->actionFactory  = $actionFactory;
        $this->eventManager   = $eventManager;
        $this->helper         = $helper;
        $this->postFactory    = $postFactory;
        $this->categoryFactory= $categoryFactory;
    }

    /**
     * @param RequestInterface $request
     * @return \Magento\Framework\App\ActionInterface|null
     */
    public function match(RequestInterface $request)
    {
        /** @var \Magento\Framework\App\Request\Http $request */
        if (!$this->dispatched) {
            if ($this->helper->isEnabled()) {
                $identifier = trim($request->getPathInfo(), '/');
                $this->eventManager->dispatch('core_controller_router_match_before', [
                    'router' => $this,
                    'condition' => new DataObject(['identifier' => $identifier, 'continue' => true])
                ]);

                $route = $this->helper->getModuleRoute();

                if ($route !== '' && $identifier === $route) {
                    $request->setModuleName('hexablog')
                        ->setControllerName('index')
                        ->setActionName('index');
                    $request->setAlias(\Magento\Framework\Url::REWRITE_REQUEST_PATH_ALIAS, $identifier);
                    $this->dispatched = true;

                    return $this->actionFactory->create(
                        'Magento\Framework\App\Action\Forward'
                    );
                }

                $pathInfo = explode('/', $identifier);
                if ($pathInfo[0] == $route && isset($pathInfo[1]) && isset($pathInfo[2])) {
                    if ($pathInfo[1] == 'post') {
                        $post = $this->postFactory->create()->load($pathInfo[2], 'identifier');
                        if ($post->getId() && $post->getIsActive()) {
                            $request->setModuleName('hexablog')
                                ->setControllerName('index')
                                ->setActionName('post')
                                ->setParam('post_object', $post);
                            $request->setAlias(\Magento\Framework\Url::REWRITE_REQUEST_PATH_ALIAS, $identifier);
                            $this->dispatched = true;

                            return $this->actionFactory->create(
                                'Magento\Framework\App\Action\Forward'
                            );
                        }
                    }

                    if ($pathInfo[1] == 'category') {
                        $category = $this->categoryFactory->create()->load($pathInfo[2], 'category_identifier');
                        if ($category->getId() && $category->getIsActive()) {
                            $request->setModuleName('hexablog')
                                ->setControllerName('index')
                                ->setActionName('category')
                                ->setParam('category_object', $category);
                            $request->setAlias(\Magento\Framework\Url::REWRITE_REQUEST_PATH_ALIAS, $identifier);
                            $this->dispatched = true;

                            return $this->actionFactory->create(
                                'Magento\Framework\App\Action\Forward'
                            );
                        }
                    }
                }
            }

            return null;
        }
    }
}
