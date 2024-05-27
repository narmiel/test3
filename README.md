Не был уверен что можно было использовать фреймворк, но в требованиях не запрещалось, поэтому сделал как быстрее, на ларе.

Она тащит лишние файлы - экземплы, но я специально не стал ничего чистить.

В качестве брокера сообщений использовался redis, для простоты, можно заменить на rabbit/sqs/whatever но суть не поменяется.

Для бизнес логики использовал сервис, который вызывается из дефолтных контроллеров, никаких бизнес экшенов, DDD и подобного.

Версионность не реализована, даже не заложена.


Как запустить проект (нужен docker, выполнить поочередно):

`cp .env.example .env`

`docker run --rm \
-u "$(id -u):$(id -g)" \
-v "$(pwd):/var/www/html" \
-w /var/www/html \
laravelsail/php83-composer:latest \
composer install --ignore-platform-reqs`

`./vendor/bin/sail up -d`

`./vendor/bin/sail artisan key:generate`

`./vendor/bin/sail artisan migrate`

Как поставить в очередь скачать рейты за прошлые 180 дней:

`./vendor/bin/sail artisan app:fill-rates 180`

Как запустить воркер, разгребающий очередь:

`./vendor/bin/sail artisan queue:work`

Пример запроса (если воркер не отработал то ничего не вернет. Не выполняет запрос real-time, если нет данных ставит в очередь):

`GET http://localhost/api/rates?date=2024-05-25&currencyCode=USD&baseCurrencyCode=EUR`

пример ответа на этот запрос:

`{"data": {"rate": "0.923860450", "difference": "0.001816045"}}`

