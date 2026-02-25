# Тестовое задание Laravel 12

Полностью рабочий проект на **Laravel 12** c **PHP 8.3+**, **PostgreSQL**, REST API, Swagger UI, WebSocket broadcasting (Pusher), demo-страницей и Docker Compose.

## Требования

- Docker
- Docker Compose

## Быстрый запуск

0. (один раз) `cp .env.example .env`
1. `docker compose up -d --build`
2. `docker compose exec app php artisan key:generate`
3. `docker compose exec app php artisan migrate --seed`
4. (опционально) `docker compose exec --user www-data app php artisan l5-swagger:generate`

Имя compose-проекта задается в `.env` (`COMPOSE_PROJECT_NAME=test_project`), поэтому команды `docker compose ...` и `./run.sh` используют один и тот же volume БД.  
После `docker compose stop` данные сохраняются. Не используйте `docker compose down -v`, если нужно сохранить БД.

### Безопасный запуск через `run.sh`

- `./run.sh` проверяет активный volume PostgreSQL и пытается восстановить данные, если обнаружен более заполненный volume с тем же проектом (варианты с `-`/`_` в имени).
- После миграций скрипт проверяет наполнение (`users/categories/products/orders`) и автоматически запускает `db:seed`, если БД неполная.

## Ссылки

- Демо-страница: http://localhost:8383/demo
- Swagger UI: http://localhost:8383/api/documentation
- Базовый URL API: http://localhost:8383/api

## Доменная модель и связи

- `User hasOne Profile` (один к одному)
- `User hasMany Orders` / `Order belongsTo User` (один ко многим / многие к одному)
- `Category hasMany Products` / `Product belongsTo Category` (один ко многим / многие к одному)
- `Order belongsToMany Products` через `order_items` (многие ко многим)
- `Product belongsToMany Orders` через `order_items` (многие ко многим)
- Pivot `order_items`: `quantity`, `price_at_purchase`

`orders.total` пересчитывается после каждого add/remove/update item:

`total = sum(quantity * price_at_purchase)`

## REST API

### Пользователи

- `GET /api/users`
- `POST /api/users`
- `GET /api/users/{id}`
- `PUT /api/users/{id}`
- `DELETE /api/users/{id}`

### Категории

- `GET /api/categories`
- `POST /api/categories`
- `GET /api/categories/{id}`
- `PUT /api/categories/{id}`
- `DELETE /api/categories/{id}`

### Товары

- `GET /api/products?category_id=`
- `POST /api/products`
- `GET /api/products/{id}`
- `PUT /api/products/{id}`
- `DELETE /api/products/{id}`

### Заказы

- `GET /api/orders?user_id=`
- `POST /api/orders`
- `GET /api/orders/{id}`
- `PUT /api/orders/{id}`
- `DELETE /api/orders/{id}`

### Позиции заказа

- `POST /api/orders/{id}/items`
- `DELETE /api/orders/{id}/items/{product_id}`

## Примеры curl (добавление/удаление позиции)

### Добавить / обновить позицию

```bash
curl -X POST http://localhost:8383/api/orders/1/items \
  -H "Content-Type: application/json" \
  -d '{"product_id":2,"quantity":3}'
```

### Удалить позицию

```bash
curl -X DELETE http://localhost:8383/api/orders/1/items/2
```

## Swagger

- UI: `/api/documentation`
- Генерация: `docker compose exec --user www-data app php artisan l5-swagger:generate`

## WebSocket / Pusher

События (`ShouldBroadcast`) публикуются в `public-dashboard`:

- `user.created`
- `user.updated`
- `product.created`
- `product.updated`
- `order.created`
- `order.updated`

По умолчанию в `docker-compose` уже подключен локальный Pusher-совместимый сервер **Soketi**:
- WebSocket: `ws://localhost:6001`
- Демо-страница `/demo` должна показывать статус сокета: **подключено**.

### Реальный Pusher

В `.env` укажите рабочие ключи:

- `PUSHER_APP_ID`
- `PUSHER_APP_KEY`
- `PUSHER_APP_SECRET`
- `PUSHER_APP_CLUSTER`

Если хотите использовать внешний Pusher вместо локального Soketi, скорректируйте:
- `PUSHER_HOST`, `PUSHER_PORT`, `PUSHER_SCHEME`
- `PUSHER_PUBLIC_HOST`, `PUSHER_PUBLIC_PORT`, `PUSHER_PUBLIC_SCHEME`

## Тесты

```bash
docker compose exec app php artisan test
```

Добавлены feature tests:

- `test_can_create_user_with_profile`
- `test_can_create_order_and_attach_item_recalculates_total`
- `test_products_filter_by_category`
