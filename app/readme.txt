
Статус elasticsearch:
sudo systemctl status elasticsearch

Перезапуск: 
sudo service elasticsearch restart

Статус индексов: 
curl -X GET "localhost:9200/_cat/indices?v"

Включение лога запросов: 
Нужно поставить название своего индекса magento2_product_1_v3
curl -X PUT "localhost:9200/magento2_product_1_v3/_settings" -H 'Content-Type: application/json' -d'
{
  "index": {
    "search.slowlog.threshold.query.warn": "1ms",
    "search.slowlog.threshold.query.info": "1ms",
    "search.slowlog.threshold.query.debug": "1ms",
    "search.slowlog.threshold.query.trace": "1ms"
  }
}'

Запуск после обновления файлов:
git pull origin master 
php bin/magento setup:di:compile
php bin/magento setup:static-content:deploy -f
php bin/magento cache:flush
sudo chmod -R 777 generated/ var/ pub/static/

// Обновление статического контента (CSS, JS, HTML) для всех тем и языков
// Выполняется для повышения производительности в production-режиме
// и после изменения темы или перевода интерфейса
php bin/magento setup:static-content:deploy

// Обновление базы данных Magento после установки или обновления модулей
// Выполняет миграции базы данных и изменения в структуре таблиц
php bin/magento setup:upgrade

// Очистка конкретных кэшированных данных (конфигурации, блоков, макетов и т.д.)
// Используется для применения изменений в системе без полного удаления кэша
php bin/magento cache:clean

// Полное удаление всех кэшированных данных, включая сторонние системы (Varnish, Redis)
// Полностью сбрасывает кэш
php bin/magento cache:flush

// Перестройка индексов для повышения производительности запросов к базе данных
// Рекомендуется выполнять после массовых обновлений данных
php bin/magento indexer:reindex

// Установка режима работы Magento: "developer" для разработки или "production" для боевой среды
// Production-режим улучшает производительность за счет предварительной компиляции
php bin/magento deploy:mode:set production

// Включение режима обслуживания, при котором сайт становится недоступным для пользователей
// Используется для технических работ
php bin/magento maintenance:enable

// Отключение режима обслуживания, делая сайт доступным для пользователей
php bin/magento maintenance:disable

// Компиляция зависимостей для ускорения работы в production-режиме
// Создает фабрики для уменьшения времени загрузки страниц
php bin/magento setup:di:compile
