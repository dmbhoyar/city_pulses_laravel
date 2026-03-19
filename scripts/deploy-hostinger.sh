#!/usr/bin/env bash
set -euo pipefail

PROJECT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
cd "$PROJECT_DIR"

echo "[Deploy] Project: $PROJECT_DIR"

if [[ ! -f ".env" ]]; then
  echo "[Deploy] .env not found. Creating from .env.example"
  cp .env.example .env
  echo "[Deploy] IMPORTANT: Update .env values in hPanel/SSH before first production run."
fi

if command -v composer >/dev/null 2>&1; then
  echo "[Deploy] Installing composer dependencies"
  composer install --no-dev --prefer-dist --optimize-autoloader --no-interaction
else
  echo "[Deploy] Composer not found on server. Ensure vendor/ exists or install Composer in Hostinger PHP settings."
fi

if grep -Eq '^APP_KEY=$' .env || ! grep -Eq '^APP_KEY=base64:' .env; then
  echo "[Deploy] Generating APP_KEY"
  php artisan key:generate --force
fi

if [[ "${RUN_MIGRATIONS:-0}" == "1" ]]; then
  echo "[Deploy] Running migrations"
  php artisan migrate --force
fi

if [[ ! -L public/storage ]]; then
  echo "[Deploy] Creating storage symlink"
  php artisan storage:link || true
fi

echo "[Deploy] Caching config/routes/views"
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "[Deploy] Done"