# Тестовое Vigrom
## Как запустить
Проект использует Docker для локальной работы.
1. Клонировать репозиторий
2. Запустить проект
   1. Вариант 1 - запустить проект с помощью make
   ```shell
   make setup
   ```
   2. Вариант 2 - запустить проект вручную
   ```shell
    docker-compose up -build
    docker-compose exec --rm php-fpm php bin/console doctrine:database:create
    docker-compose exec --rm php-fpm php bin/console doctrine:migrations:migrate --no-interaction
    docker-compose exec --rm php-fpm php bin/console lexik:jwt:generate-keypair
    docker-compose exec --rm php-fpm php bin/console app:init
   ```

## Как использовать
Залогиниться от имени тестового пользователя:
```
### Login
POST http://localhost/api/login_check
Content-Type: application/json
Accept: application/json

{
  "username": "alice@mail.com",
  "password": "alice_123"
}


### Curl
curl -X POST --location "http://localhost/api/login_check" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json" \
    -d "{
          \"username\": \"alice@mail.com\",
          \"password\": \"alice_123\"
        }"
```
В заголовке прокидывать полученный токен через 

`Authorization: Bearer {{token}}`

Баланс кошелька
```shell
### Get wallet
GET http://localhost/api/wallet/1
Authorization: Bearer {{token}}
Content-Type: application/json
Accept: application/json

### curl
curl -X GET --location "http://localhost/api/wallet/1" \
    -H "Authorization: Bearer TOKEN" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

Выполнение транзакции
```shell
### Do transaction
POST http://localhost/api/wallet/1
Authorization: Bearer {{token}}
Content-Type: application/json
Accept: application/json

{
  "type": "credit",
  "amount": 1,
  "currency": "USD",
  "reason": "stock"
}

### curl
curl -X POST --location "http://localhost/api/wallet/1" \
    -H "Authorization: Bearer TOKEN" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json" \
    -d "{
          \"type\": \"credit\",
          \"amount\": 1,
          \"currency\": \"USD\",
          \"reason\": \"stock\"
        }"
```
Лог транзакций кошелька
```shell
### Get Transactions log
GET http://localhost/api/wallet/1/log
Authorization: Bearer {{token}}
Content-Type: application/json
Accept: application/json

### curl
curl -X GET --location "http://localhost/api/wallet/1/log" \
    -H "Authorization: Bearer TOKEN" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```
Лог рефандов за прошедшие 7 дней
```shell
### Get Transactions log
GET http://localhost/api/wallet/1/log?last_week_refunds=true
Authorization: Bearer {{token}}
Content-Type: application/json
Accept: application/json
```