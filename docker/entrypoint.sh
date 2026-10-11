#!/bin/bash
set -e
cd /var/www

echo "[archer] menyiapkan aplikasi..."

# 1) .env
if [ ! -f .env ]; then
    cp .env.example .env
    echo "[archer] .env dibuat dari .env.example"
fi

# 2) Arahkan .env ke service Docker (DB di host 'db')
set_env () { # key value
    if grep -q "^$1=" .env; then
        sed -i "s#^$1=.*#$1=$2#" .env
    else
        echo "$1=$2" >> .env
    fi
}
set_env APP_ENV "${APP_ENV:-local}"
set_env APP_DEBUG "${APP_DEBUG:-true}"
set_env APP_URL "${APP_URL:-http://localhost:8080}"
set_env DB_CONNECTION mysql
set_env DB_HOST "${DB_HOST:-db}"
set_env DB_PORT "${DB_PORT:-3306}"
set_env DB_DATABASE "${DB_DATABASE:-archer}"
set_env DB_USERNAME "${DB_USERNAME:-archer}"
set_env DB_PASSWORD "${DB_PASSWORD:-secret}"

# 3) Dependensi (install; bila composer.lock tidak sinkron, update otomatis)
if [ ! -d vendor ]; then
    echo "[archer] menjalankan composer install..."
    composer install --prefer-dist --optimize-autoloader \
        || composer update --prefer-dist --optimize-autoloader
fi

# 4) Kunci aplikasi
if ! grep -q "^APP_KEY=base64:" .env; then
    php artisan key:generate --force
fi

# 5) Folder & izin tulis
mkdir -p storage/framework/{cache,sessions,views} storage/logs bootstrap/cache public/uploads/athletes
chmod -R 775 storage bootstrap/cache public/uploads || true
chown -R www-data:www-data storage bootstrap/cache public/uploads || true

# 6) Tunggu database siap
echo "[archer] menunggu database ${DB_HOST:-db}..."
until php -r "new PDO('mysql:host=${DB_HOST:-db};port=${DB_PORT:-3306};dbname=${DB_DATABASE:-archer}', '${DB_USERNAME:-archer}', '${DB_PASSWORD:-secret}');" >/dev/null 2>&1; do
    sleep 2
done

# 7) Migrasi (+ seed opsional: set SEED=true)
php artisan migrate --force
if [ "${SEED:-false}" = "true" ]; then
    echo "[archer] menjalankan seeder..."
    php artisan db:seed --force
fi

php artisan config:clear >/dev/null 2>&1 || true

echo "[archer] siap."
exec "$@"
