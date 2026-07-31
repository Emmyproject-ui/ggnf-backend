#!/bin/bash
set -e

echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo "  GGNF Laravel — Container startup"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"

# ── Render injects $PORT (usually 10000) ────────────────────
export PORT="${PORT:-10000}"
echo "🌐 Will listen on port ${PORT}"

# ── Build nginx config from template ────────────────────────
echo "⚙️  Configuring Nginx (port=${PORT})..."
envsubst '${PORT}' < /etc/nginx/nginx.conf.template > /etc/nginx/http.d/default.conf

# ── Ensure storage structures exist at runtime ───────────────
echo "⚙️  Ensuring runtime storage directories..."
mkdir -p storage/framework/{sessions,views,cache}
mkdir -p storage/logs
chown -R nobody:nobody storage bootstrap/cache
chmod -R 775 storage bootstrap/cache


# ── Wait for Aiven MySQL ─────────────────────────────────────
DB_HOST="${DB_HOST:-mysql-2fd82d31-emmanuelnwigwe87-d318.l.aivencloud.com}"
DB_PORT="${DB_PORT:-16755}"
DB_DATABASE="${DB_DATABASE:-defaultdb}"
DB_USERNAME="${DB_USERNAME:-avnadmin}"
DB_PASSWORD="${DB_PASSWORD}"
MAX_TRIES=30
TRIES=0

echo "⏳ Waiting for Aiven MySQL at ${DB_HOST}:${DB_PORT}..."
until php -r "
    try {
        \$pdo = new PDO(
            'mysql:host=${DB_HOST};port=${DB_PORT};dbname=${DB_DATABASE}',
            '${DB_USERNAME}',
            '${DB_PASSWORD}',
            [PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT => false]
        );
        echo 'connected';
    } catch (Exception \$e) {
        exit(1);
    }
" 2>/dev/null | grep -q connected; do
    TRIES=$((TRIES+1))
    if [ $TRIES -ge $MAX_TRIES ]; then
        echo "❌ Could not connect to MySQL after ${MAX_TRIES} attempts. Exiting."
        exit 1
    fi
    echo "   Attempt ${TRIES}/${MAX_TRIES} — retrying in 2s..."
    sleep 2
done

echo "✅ MySQL is ready."

# ── Run migrations ───────────────────────────────────────────
echo "⚙️  Running migrations..."
php artisan migrate --force --no-interaction

# ── Seed admin user (updateOrCreate — safe to re-run) ────────
echo "⚙️  Seeding admin user..."
php artisan db:seed --class=AdminUserSeeder --force --no-interaction

# ── Cache config/routes/views ────────────────────────────────
echo "⚙️  Caching configuration..."
php artisan config:clear
php artisan config:cache
php artisan route:clear
php artisan route:cache
php artisan view:clear
php artisan view:cache

# ── Create storage symlink (idempotent) ─────────────────────
if [ ! -L public/storage ]; then
    echo "⚙️  Creating storage symlink..."
    php artisan storage:link
fi

echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo "  Startup complete — launching services"
echo "  PHP-FPM :9000  |  Nginx :${PORT}"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"

exec /usr/bin/supervisord -c /etc/supervisord.conf
