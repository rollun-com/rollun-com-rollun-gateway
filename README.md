# rollun-gateway
---

## Api Gateway
Сервис который проксирует через себя все запросы,
 и перенаправляет их в конкретные сервисы.
[Более детально о патерне микросервисной архитектуры - api-gateway](http://microservices.io/patterns/apigateway.html)

Все внутренние сервисы, общаются между собой исключительно через api-gateway. 
Используя указанный ниже протокол общения.

## Принцип работы

Маска запроса `{services}.domain.com/{send_path}`
> Поддерживаемый типы запроса - все согласно стандарта http
* services - имя сервиса в который будет передан запрос
* send_path - путь по которому будет отправлен запрос в сервис

### Пример  

Допустим мы отправим запрос на gateway.
```
GET
http://amazonStore.gateway/api/webhook/withdraw?asin="AAAAAAAA"&sku="AA-AAA-AAA"
```
Он в свою очередь, примет это запрос, разберет его и переотправит на сервис с именем `amazonStore`.
Допустим, у нас есть конфиг соответсвия.
```
amazonStore: 192.168.122.23
amazonStore: 192.168.123.23
```
Тогда имени `amazonStore` будет соответсвовать ip `192.168.122.23`.
Переотправленый запрос будет выглядеть  
```
GET
http://192.168.122.23/api/webhook/withdraw?asin="AAAAAAAA"&sku="AA-AAA-AAA"
```
После того как запрос отработает, **api-gateway** вернет ответ.

### Service Register
Для резолва сервисов, используется библиотека [rollun-mesh](https://github.com/rollun-com/rollun-mesh), а конкретно MeshHttpClient.
Для того что бы добавить новый сервис, вам нужно добавить его в MeshService. 

## Проблемы с проксированием запросов типа multipart/form-data
По умолчаниб запросы c *Content-Type=multipart/form-data* не доступны в php как *raw body*
> [php:// - php.net](http://php.net/manual/ru/wrappers.php.php)

Для решения этой проблемы используется настройка apache, которам переопределяет тип заголовка 
*Content-Type* с *multipart/form-data* на *application/x-httpd-php*, а оригинальный тип кладет 
в заголовок *X-Real-Content-Type*. 
Так что для коректной работы *api-gateway* Вам необходимо добавить в .htaccess сделующие строки с правилами.
    
```apacheconfig
SetEnvIf Content-Type ^(multipart/form-data)(.*) MULTIPART_CTYPE=$1$2
RequestHeader set Content-Type application/x-httpd-php env=MULTIPART_CTYPE
RequestHeader set X-Real-Content-Type %{MULTIPART_CTYPE}e env=MULTIPART_CTYPE
```

> api-gateway при проксирование запроса, заменит заголовки на исходые, тем самым сервис к которому 
будет отправлен запрос, не заметим подмены.
