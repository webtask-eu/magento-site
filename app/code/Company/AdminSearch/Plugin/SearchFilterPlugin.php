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

    // Метод для получения актуального индекса
    private function getCurrentIndex($pattern = 'magento2_product_*')
{
    try {
        // Параметры для получения всех индексов
        $params = [
            'index' => '_all',  // Используем '_all', чтобы получить все индексы
            'format' => 'json'
        ];
        $response = $this->elasticsearchClient->cat()->indices($params);

        $this->logger->debug('Список индексов от Elasticsearch: ' . json_encode($response));

        // Фильтруем индексы по паттерну
        $matchingIndices = [];
        foreach ($response as $indexInfo) {
            if (preg_match("/^$pattern$/", $indexInfo['index'])) {
                $matchingIndices[] = $indexInfo['index'];
            }
        }

        // Находим индекс с самой новой версией
        $latestIndex = '';
        $latestVersion = 0;
        foreach ($matchingIndices as $index) {
            preg_match('/_(v\d+)$/', $index, $matches);
            if (isset($matches[1]) && (int)filter_var($matches[1], FILTER_SANITIZE_NUMBER_INT) > $latestVersion) {
                $latestVersion = (int)filter_var($matches[1], FILTER_SANITIZE_NUMBER_INT);
                $latestIndex = $index;
            }
        }

        // Если нашли подходящий индекс, возвращаем его
        if ($latestIndex) {
            $this->logger->debug('Актуальный индекс: ' . $latestIndex);
            return $latestIndex;
        } else {
            // Если подходящий индекс не найден, логируем и возвращаем значение по умолчанию
            $this->logger->error('Не удалось найти подходящий индекс по паттерну: ' . $pattern);
            return 'magento2_product_'; // Значение по умолчанию в случае ошибки
        }
    } catch (\Exception $e) {
        $this->logger->error('Ошибка при получении актуального индекса: ' . $e->getMessage());
        return 'magento2_product_'; // Значение по умолчанию в случае ошибки
    }
}


    // Получение продуктов из Elasticsearch
    private function getProductsFromElasticsearch($searchQuery)
    {
        // Получаем актуальный индекс
        $currentIndex = $this->getCurrentIndex();

        $params = [
            'index' => $currentIndex,  // Используем актуальный индекс
            'body'  => [
                'query' => [
                    'bool' => [  // Логический оператор "bool" позволяет комбинировать несколько условий поиска
                        'should' => [  // Используем "should", чтобы запрос удовлетворял хотя бы одному из следующих условий
                            [
                                'match_phrase_prefix' => [  // Поиск по фразе с началом совпадения (префиксный поиск) по полю 'sku'
                                    'sku' => [
                                        'query' => $searchQuery,  // Поисковый запрос (введённый пользователем)
                                        'slop' => 0,  // Указывает, что не допускаются изменения порядка слов (в данном случае это неактуально, так как используется одно слово)
                                        'max_expansions' => 50,  // Максимальное количество расширений для поиска с учетом префиксов
                                        'boost' => 7.0  // Значение приоритета (вес) для этого поля в результатах поиска
                                    ]
                                ]
                            ],
                            [
                                'match_phrase_prefix' => [  // Поиск по фразе с началом совпадения по полю 'name'
                                    'name' => [
                                        'query' => $searchQuery,  // Поисковый запрос для поля 'name'
                                        'slop' => 0,  // Точное совпадение порядка слов
                                        'max_expansions' => 50,  // Максимальное количество префиксных совпадений
                                        'boost' => 6.0  // Значение приоритета для поля 'name' (чуть ниже, чем для SKU)
                                    ]
                                ]
                            ],
                            [
                                'match' => [  // Обычный поиск по полю 'manufacturer_value'
                                    'manufacturer_value' => [
                                        'query' => $searchQuery,  // Поисковый запрос для поля 'manufacturer_value'
                                        'operator' => 'OR',  // Оператор "OR" для возможности поиска по нескольким словам
                                        'fuzzy_transpositions' => true,  // Разрешаем незначительные изменения в последовательности символов для нахождения похожих слов
                                        'boost' => 2.0  // Вес этого поля ниже, так как это менее важное поле для поиска
                                    ]
                                ]
                            ],
                            [
                                'match' => [  // Поиск по полю 'status_value'
                                    'status_value' => [
                                        'query' => $searchQuery,  // Поисковый запрос для поля 'status_value'
                                        'operator' => 'OR',  // Оператор "OR" для многословного запроса
                                        'fuzzy_transpositions' => true,  // Разрешаем небольшие опечатки в запросе
                                        'boost' => 2.0  // Низкий приоритет поля 'status_value'
                                    ]
                                ]
                            ],
                            [
                                'match' => [  // Поиск по полю 'url_key'
                                    'url_key' => [
                                        'query' => $searchQuery,  // Поисковый запрос для поля 'url_key'
                                        'operator' => 'OR',  // Оператор "OR" для поиска по нескольким словам
                                        'fuzzy_transpositions' => true,  // Небольшие изменения в порядке символов допустимы
                                        'boost' => 2.0  // Низкий вес для этого поля
                                    ]
                                ]
                            ]
                        ],
                        'minimum_should_match' => 1,  // Должно совпадать как минимум одно условие из списка 'should'
                        'boost' => 1.0  // Общий вес для всей логической конструкции
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
