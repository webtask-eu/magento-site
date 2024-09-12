
Статус elasticsearch:
sudo systemctl status elasticsearch

Статус индексов: 
curl -X GET "localhost:9200/_cat/indices?v"
