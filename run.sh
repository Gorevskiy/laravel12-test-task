#!/usr/bin/env bash
set -euo pipefail

ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
cd "$ROOT_DIR"

# Фиксируем имя compose-проекта, чтобы всегда использовался один и тот же volume БД.
export COMPOSE_PROJECT_NAME="${COMPOSE_PROJECT_NAME:-test_project}"

if ! command -v docker >/dev/null 2>&1; then
  echo "Ошибка: docker не установлен."
  exit 1
fi

if ! docker compose version >/dev/null 2>&1; then
  echo "Ошибка: docker compose недоступен."
  exit 1
fi

echo "Запускаю контейнеры..."
docker compose up -d --build

echo "Применяю миграции (без удаления данных)..."
docker compose exec app php artisan migrate --force

echo "Генерирую APP_KEY..."
#docker compose exec app php artisan key:generate --force

# echo "Полностью пересоздать БД и заполнить тестовыми данными:"
# docker compose exec app php artisan migrate:fresh --seed --force

echo "Генерирую Swagger-документацию..."
docker compose exec app php artisan l5-swagger:generate

echo
echo "Готово."
echo "Демо:    http://localhost:8383/demo"
echo "Swagger: http://localhost:8383/api/documentation"
