<?php
namespace Magefan\Blog\Ui\DataProvider\Post\Related\ProductDataProvider;

/**
 * Interceptor class for @see \Magefan\Blog\Ui\DataProvider\Post\Related\ProductDataProvider
 */
class Interceptor extends \Magefan\Blog\Ui\DataProvider\Post\Related\ProductDataProvider implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct($name, $primaryFieldName, $requestFieldName, \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $collectionFactory, \Magento\Framework\App\RequestInterface $request, \Magento\Catalog\Api\ProductRepositoryInterface $productRepository, \Magento\Catalog\Api\ProductLinkRepositoryInterface $productLinkRepository, $addFieldStrategies, $addFilterStrategies, array $meta = [], array $data = [])
    {
        $this->___init();
        parent::__construct($name, $primaryFieldName, $requestFieldName, $collectionFactory, $request, $productRepository, $productLinkRepository, $addFieldStrategies, $addFilterStrategies, $meta, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function getData()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getData');
        return $pluginInfo ? $this->___callPlugins('getData', func_get_args(), $pluginInfo) : parent::getData();
    }
}
