<?php
namespace Company\AdminSearch\Plugin;

use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Elasticsearch7\Model\Client\Elasticsearch;
use Psr\Log\LoggerInterface;

class ProductSearch
{
    protected $elasticsearchClient;
    protected $productCollectionFactory;
    protected $logger;

    public function __construct(
        Elasticsearch $elasticsearchClient,
        CollectionFactory $productCollectionFactory,
        LoggerInterface $logger
    ) {
        $this->elasticsearchClient = $elasticsearchClient;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->logger = $logger;
    }

    public function aroundGetCollection(\Magento\Catalog\Model\Product $subject, callable $proceed, ...$args)
    {
        // Добавляем логирование для отладки
        $this->logger->info('Плагин перехвата поиска активирован.');

        // Получаем запрос поиска (нужно адаптировать для реального запроса, если он приходит через аргументы или глобальный запрос)
        $searchQuery = isset($args[0]) ? $args[0] : ''; // Это нужно адаптировать в зависимости от того, откуда берется поисковый запрос

        // Если запрос пустой, вызываем стандартную логику
        if (empty($searchQuery)) {
            $this->logger->info('Пустой запрос. Возвращаем стандартный результат.');
            return $proceed(...$args);
        }

        try {
            // Логируем параметры запроса для Elasticsearch
            $params = [
                'index' => 'magento2_product_1_v3',  // Убедитесь, что индекс правильный
                'body'  => [
                    'query' => [
                        'match' => [
                            'name' => $searchQuery  // Ищем по полю "name"
                        ]
                    ]
                ]
            ];
            $this->logger->info('Отправка запроса в Elasticsearch', ['params' => $params]);

            // Выполняем запрос к Elasticsearch
            $searchResults = $this->elasticsearchClient->search($params);
            $this->logger->info('Результаты поиска из Elasticsearch получены.', ['results' => $searchResults]);

            // Получаем ID продуктов из результатов поиска
            $productIds = [];
            foreach ($searchResults['hits']['hits'] as $hit) {
                $productIds[] = $hit['_id'];
            }

            // Логируем найденные ID продуктов
            $this->logger->info('Найденные ID продуктов', ['productIds' => $productIds]);

            // Если продукты найдены, загружаем коллекцию
            if (!empty($productIds)) {
                $collection = $this->productCollectionFactory->create()
                    ->addFieldToFilter('entity_id', ['in' => $productIds]);

                $this->logger->info('Возвращаем коллекцию продуктов из Elasticsearch.');
                return $collection;
            } else {
                $this->logger->info('Продукты не найдены. Возвращаем стандартный результат.');
                return $proceed(...$args);
            }
        } catch (\Exception $e) {
            // Логируем ошибку
            $this->logger->error('Ошибка при запросе к Elasticsearch: ' . $e->getMessage());

            // Возвращаем стандартный результат в случае ошибки
            return $proceed(...$args);
        }
    }
}
