<?php
namespace Company\AdminSearch\Plugin;

use Magento\Catalog\Model\ResourceModel\Product\Collection as ProductCollection;
use Magento\Framework\App\RequestInterface;
use Psr\Log\LoggerInterface;
use Elasticsearch\ClientBuilder;

class SearchFilterPlugin
{
    protected $logger;
    protected $elasticsearchClient;
    protected $request;
    protected $alreadyFiltered = false; // Флаг для предотвращения зацикливания

    public function __construct(
        LoggerInterface $logger,
        RequestInterface $request
    ) {
        $this->logger = $logger;
        $this->request = $request;

        try {
            $this->elasticsearchClient = ClientBuilder::create()->build();
            $this->logger->debug('Elasticsearch клиент успешно инициализирован');
        } catch (\Exception $e) {
            $this->logger->error('Ошибка инициализации Elasticsearch клиента: ' . $e->getMessage());
        }
    }

    // Перехватываем метод load() для теста
    public function aroundLoad(ProductCollection $subject, callable $proceed, ...$args)
    {
        $this->logger->debug('Перехвачен метод load()');
        
        if ($this->alreadyFiltered) {
            // Если фильтрация уже применена, продолжаем выполнение без повторной фильтрации
            return $proceed(...$args);
        }

        // Получаем параметр search из глобального запроса
        $searchQuery = $this->request->getParam('search');
        $this->logger->debug('Параметр search из запроса: ' . ($searchQuery ? $searchQuery : 'Пусто'));
    
        if ($searchQuery) {
            $this->logger->debug('Поисковый запрос: ' . $searchQuery);
    
            // Elasticsearch logic
            $productIds = $this->getProductsFromElasticsearch($searchQuery);
            if (!empty($productIds)) {
                $this->logger->debug('Применяем фильтр по entity_id: ' . implode(', ', $productIds));
                $subject->addFieldToFilter('entity_id', ['in' => $productIds]);

                // Логируем SQL-запрос, который будет выполнен
                $this->logger->debug('SQL Query после добавления фильтра: ' . $subject->getSelect()->__toString());

                // Устанавливаем флаг, чтобы избежать зацикливания
                $this->alreadyFiltered = true;

                // Выполняем фильтр и логируем результат
                $results = $subject->load();
                $this->logger->debug('Результаты после фильтрации: ' . json_encode($results->toArray()));

                return $subject;
            } else {
                $this->logger->debug('Результаты Elasticsearch пусты. Применяем фильтр entity_id = -1');
                $subject->addFieldToFilter('entity_id', ['eq' => '-1']);

                // Логируем SQL-запрос, который будет выполнен
                $this->logger->debug('SQL Query после фильтра: ' . $subject->getSelect()->__toString());

                return $subject;
            }
        }
    
        return $proceed(...$args);
    }

    // Логируем и фильтруем запросы
    private function interceptQuery(ProductCollection $subject, callable $proceed, ...$args)
    {
        $searchQuery = $this->request->getParam('search');
        $this->logger->debug('Параметр search из запроса: ' . ($searchQuery ? $searchQuery : 'Пусто'));

        if ($searchQuery) {
            $this->logger->debug('Поисковый запрос: ' . $searchQuery);

            // Elasticsearch logic
            $productIds = $this->getProductsFromElasticsearch($searchQuery);
            if (!empty($productIds)) {
                $this->logger->debug('Применяем фильтр по entity_id: ' . implode(', ', $productIds));
                $subject->addFieldToFilter('entity_id', ['in' => $productIds]);

                // Логируем SQL-запрос
                $this->logger->debug('SQL Query после добавления фильтра: ' . $subject->getSelect()->__toString());

                return $subject;
            } else {
                $this->logger->debug('Результаты Elasticsearch пусты. Применяем фильтр entity_id = -1');
                $subject->addFieldToFilter('entity_id', ['eq' => '-1']);

                // Логируем SQL-запрос
                $this->logger->debug('SQL Query после фильтра: ' . $subject->getSelect()->__toString());

                return $subject;
            }
        }

        return $proceed(...$args);
    }

    // Получение продуктов из Elasticsearch
    private function getProductsFromElasticsearch($searchQuery)
    {

        $params = [
        'index' => 'magento2_product_1_v3',
        'body'  => [
            'query' => [
                'wildcard' => [
                    'sku' => '*' . $searchQuery . '*' // Добавление wildcard для поиска по части строки
                ]
            ]
        ]
    ];

		
        try {
            $this->logger->debug('Запрос к Elasticsearch: ' . json_encode($params));
            $results = $this->elasticsearchClient->search($params);
            $this->logger->debug('Результаты от Elasticsearch: ' . json_encode($results));

            // Получение всех entity_id из результатов Elasticsearch
            $productIds = [];
            foreach ($results['hits']['hits'] as $hit) {
                $productIds[] = $hit['_id'];
            }

            $this->logger->debug('Идентификаторы продуктов: ' . implode(', ', $productIds));
            return $productIds;
        } catch (\Exception $e) {
            $this->logger->error('Ошибка при выполнении запроса к Elasticsearch: ' . $e->getMessage());
            return [];
        }
    }
}
