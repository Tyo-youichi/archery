#!/bin/bash
set -e
cd /var/www

echo "[archer] menyiapkan aplikasi..."

# 1) .env
if [ ! -f .env ]; then
    cp .env.example .env
    echo "[archer] .env dibuat dari .env.example"
fi

# 2) Arahkan .env ke environment (DB eksternal via host.docker.internal secara default)
set_env () { # key value
    if grep -q "^$1=" .env; then
        sed -i "s#^$1=.*#$1=$2#" .env
    else
        echo "$1=$2" >> .env
    fi
}
set_env APP_ENV "${APP_ENV:-local}"
set_env APP_DEBUG "${APP_DEBUG:-true}"
set_env APP_URL "${APP_URL:-http://localhost:7777}"
set_env DB_CONNECTION mysql
set_env DB_HOST "${DB_HOST:-host.docker.internal}"
set_env DB_PORT "${DB_PORT:-3306}"
set_env DB_DATABASE "${DB_DATABASE:-archer}"
set_env DB_USERNAME "${DB_USERNAME:-archer}"
set_env DB_PASSWORD "${DB_PASSWORD:-secret}"

# 3) Dependensi (vendor sudah di-bake saat build; ini fallback bila kode di-bind-mount)
if [ ! -d vendor ]; then
    echo "[archer] menjalankan composer install..."
    composer install --prefer-dist --optimize-autoloader \
        || composer update --prefer-dist --optimize-autoloader
fi

# 4) Kunci aplikasi
if ! grep -q "^APP_KEY=base64:" .env; then
    php artisan key:generate --force
fi

# 5) Folder & izin tulis (storage & public/uploads di-mount sebagai volume → siapkan isinya)
mkdir -p storage/framework/{cache,sessions,views} storage/logs storage/app/public public/uploads/athletes
chmod -R 775 storage bootstrap/cache public/uploads || true
chown -R www-data:www-data storage bootstrap/cache public/uploads || true

# 6) Tunggu database siap
echo "[archer] menunggu database ${DB_HOST}:${DB_PORT}..."
until php -r "new PDO('mysql:host=${DB_HOST};port=${DB_PORT};dbname=${DB_DATABASE}', '${DB_USERNAME}', '${DB_PASSWORD}');" >/dev/null 2>&1; do
    sleep 2
done

# 7) Migrasi (+ seed opsional: set SEED=true)
php artisan migrate --force
if [ "${SEED:-false}" = "true" ]; then
    echo "[archer] menjalankan seeder (DemoSeeder)..."
    php artisan db:seed --class=DemoSeeder --force
fi

php artisan optimize:clear >/dev/null 2>&1 || true

echo "[archer] siap di port dalam-container 80 (host: ${APP_PORT:-7777})."
exec "$@"
