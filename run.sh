#!/usr/bin/env bash
set -euo pipefail

ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
cd "$ROOT_DIR"

PROJECT_NAME="${COMPOSE_PROJECT_NAME:-}"

if [[ -z "$PROJECT_NAME" && -f .env ]]; then
  PROJECT_NAME="$(awk -F= '/^COMPOSE_PROJECT_NAME=/{print $2}' .env | tr -d '"' | tr -d "'" | head -n1)"
fi

if [[ -z "$PROJECT_NAME" ]]; then
  PROJECT_NAME="$(docker compose config --no-interpolate 2>/dev/null | awk '/^name:/{print $2; exit}')"
fi

if [[ -z "$PROJECT_NAME" ]]; then
  PROJECT_NAME="$(basename "$ROOT_DIR" | tr -c '[:alnum:]' '_')"
fi

ACTIVE_VOLUME="${PROJECT_NAME}_postgres_data"

if ! command -v docker >/dev/null 2>&1; then
  echo "Ошибка: docker не установлен."
  exit 1
fi

if ! docker compose version >/dev/null 2>&1; then
  echo "Ошибка: docker compose недоступен."
  exit 1
fi

probe_volume_users_count() {
  local volume="$1"

  if ! docker volume inspect "$volume" >/dev/null 2>&1; then
    echo "-1"
    return
  fi

  local container_name="pg_probe_${volume//[^a-zA-Z0-9]/_}_$RANDOM"
  docker run -d --name "$container_name" \
    -e POSTGRES_DB=laravel \
    -e POSTGRES_USER=laravel \
    -e POSTGRES_PASSWORD=laravel \
    -v "$volume":/var/lib/postgresql/data \
    postgres:16-alpine postgres -p 5432 >/dev/null

  local ready="0"
  for _ in $(seq 1 45); do
    if docker exec "$container_name" pg_isready -U laravel -d laravel -p 5432 >/dev/null 2>&1; then
      ready="1"
      break
    fi
    sleep 1
  done

  if [[ "$ready" != "1" ]]; then
    docker rm -f "$container_name" >/dev/null 2>&1 || true
    echo "-1"
    return
  fi

  local has_users_table
  has_users_table="$(docker exec "$container_name" psql -U laravel -d laravel -p 5432 -At -c "SELECT EXISTS (SELECT 1 FROM information_schema.tables WHERE table_schema='public' AND table_name='users');" 2>/dev/null | tr -d '[:space:]' || true)"

  if [[ "$has_users_table" != "t" ]]; then
    docker rm -f "$container_name" >/dev/null 2>&1 || true
    echo "0"
    return
  fi

  local users_count
  users_count="$(docker exec "$container_name" psql -U laravel -d laravel -p 5432 -At -c "SELECT COUNT(*) FROM users;" 2>/dev/null | tr -d '[:space:]' || true)"
  docker rm -f "$container_name" >/dev/null 2>&1 || true

  if [[ "$users_count" =~ ^[0-9]+$ ]]; then
    echo "$users_count"
  else
    echo "0"
  fi
}

restore_richer_volume_if_needed() {
  local active_count best_count best_volume
  local alt_a alt_b

  alt_a="${PROJECT_NAME//_/-}_postgres_data"
  alt_b="${PROJECT_NAME//-/_}_postgres_data"

  docker volume create "$ACTIVE_VOLUME" >/dev/null

  active_count="$(probe_volume_users_count "$ACTIVE_VOLUME")"
  if ! [[ "$active_count" =~ ^-?[0-9]+$ ]]; then
    active_count="0"
  fi
  if (( active_count < 0 )); then
    active_count="0"
  fi

  best_count="$active_count"
  best_volume="$ACTIVE_VOLUME"

  for volume in "$alt_a" "$alt_b"; do
    if [[ "$volume" == "$ACTIVE_VOLUME" ]]; then
      continue
    fi

    local count
    count="$(probe_volume_users_count "$volume")"
    if ! [[ "$count" =~ ^-?[0-9]+$ ]]; then
      continue
    fi
    if (( count > best_count )); then
      best_count="$count"
      best_volume="$volume"
    fi
  done

  if (( active_count == 0 )) && (( best_count > 0 )) && [[ "$best_volume" != "$ACTIVE_VOLUME" ]]; then
    echo "Найден более заполненный volume: $best_volume (users=$best_count). Восстанавливаю данные в $ACTIVE_VOLUME..."
    docker run --rm \
      -v "$best_volume":/from \
      -v "$ACTIVE_VOLUME":/to \
      alpine sh -lc "rm -rf /to/* /to/.[!.]* /to/..?* 2>/dev/null || true; cd /from && tar cf - . | (cd /to && tar xpf -)"
  fi

  local final_count
  final_count="$(probe_volume_users_count "$ACTIVE_VOLUME")"
  if ! [[ "$final_count" =~ ^[0-9]+$ ]]; then
    final_count="0"
  fi
  echo "Активный PostgreSQL volume: $ACTIVE_VOLUME (users=$final_count)"
}

restore_richer_volume_if_needed

echo "Запускаю контейнеры..."
docker compose up -d --build

echo "Применяю миграции (без удаления данных)..."
docker compose exec app php artisan migrate --force

echo "Проверяю наполнение БД..."
COUNTS_ROW="$(docker compose exec -T postgres psql -U laravel -d laravel -p 54322 -At -c "SELECT (SELECT COUNT(*) FROM users), (SELECT COUNT(*) FROM categories), (SELECT COUNT(*) FROM products), (SELECT COUNT(*) FROM orders);" | head -n1)"
IFS='|' read -r USERS_COUNT CATEGORIES_COUNT PRODUCTS_COUNT ORDERS_COUNT <<< "$COUNTS_ROW"
USERS_COUNT="${USERS_COUNT:-0}"
CATEGORIES_COUNT="${CATEGORIES_COUNT:-0}"
PRODUCTS_COUNT="${PRODUCTS_COUNT:-0}"
ORDERS_COUNT="${ORDERS_COUNT:-0}"

if (( USERS_COUNT < 3 || CATEGORIES_COUNT < 3 || PRODUCTS_COUNT < 10 || ORDERS_COUNT < 3 )); then
  echo "БД неполная (users=$USERS_COUNT, categories=$CATEGORIES_COUNT, products=$PRODUCTS_COUNT, orders=$ORDERS_COUNT). Запускаю db:seed..."
  docker compose exec app php artisan db:seed --force
fi

echo "Проверяю права на storage и cache..."
docker compose exec app sh -lc "mkdir -p storage/api-docs && chown -R www-data:www-data storage bootstrap/cache"

echo "Генерирую APP_KEY..."
#docker compose exec app php artisan key:generate --force

# echo "Полностью пересоздать БД и заполнить тестовыми данными:"
# docker compose exec app php artisan migrate:fresh --seed --force

echo "Генерирую Swagger-документацию..."
docker compose exec --user www-data app php artisan l5-swagger:generate

echo "Итоговое состояние БД:"
docker compose exec -T postgres psql -U laravel -d laravel -p 54322 -c "SELECT 'users' AS tbl, COUNT(*) AS cnt FROM users UNION ALL SELECT 'categories', COUNT(*) FROM categories UNION ALL SELECT 'products', COUNT(*) FROM products UNION ALL SELECT 'orders', COUNT(*) FROM orders UNION ALL SELECT 'order_items', COUNT(*) FROM order_items ORDER BY tbl;"

echo
echo "Готово."
echo "Демо:    http://localhost:8383/demo"
echo "Swagger: http://localhost:8383/api/documentation"
