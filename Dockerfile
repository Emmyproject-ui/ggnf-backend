# ============================================================
# Stage 1: Composer dependencies
# ============================================================
FROM composer:2.7 AS composer_stage

WORKDIR /app

COPY composer.json composer.lock ./

RUN composer install \
    --no-dev \
    --no-interaction \
    --no-scripts \
    --prefer-dist \
    --optimize-autoloader

# ============================================================
# Stage 2: Production image (PHP-FPM + Nginx + Supervisor)
# ============================================================
FROM php:8.2-fpm-alpine AS production

LABEL maintainer="GGNF <noreply@ggnf.org>"

# ── System dependencies ──────────────────────────────────────
# gettext provides envsubst (for PORT substitution in nginx config)
RUN apk add --no-cache \
    bash \
    curl \
    nginx \
    supervisor \
    gettext \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    libzip-dev \
    zip \
    unzip \
    oniguruma-dev \
    icu-dev \
    linux-headers \
    shadow

# ── PHP extensions ───────────────────────────────────────────
RUN docker-php-ext-configure gd \
        --with-freetype \
        --with-jpeg \
 && docker-php-ext-install -j$(nproc) \
        pdo_mysql \
        mbstring \
        exif \
        pcntl \
        bcmath \
        gd \
        zip \
        intl \
        opcache

# ── PHP production ini ───────────────────────────────────────
COPY docker/php/php.ini /usr/local/etc/php/conf.d/app.ini

# ── Supervisor config ────────────────────────────────────────
COPY docker/supervisord.conf /etc/supervisord.conf

# ── Nginx config template (PORT is injected by Render) ───────
COPY docker/nginx/nginx.conf.template /etc/nginx/nginx.conf.template

# ── Nginx log dirs ───────────────────────────────────────────
RUN mkdir -p /var/log/nginx /var/lib/nginx/tmp/client_body \
 && chown -R nobody:nobody /var/lib/nginx

WORKDIR /var/www/html

# ── Copy Composer-built vendor from stage 1 ─────────────────
COPY --from=composer_stage /app/vendor ./vendor

# ── Copy application source ──────────────────────────────────
COPY --chown=nobody:nobody . .

# ── Storage permissions ──────────────────────────────────────
RUN mkdir -p storage/framework/{sessions,views,cache} \
             storage/logs \
             bootstrap/cache \
 && chmod -R 775 storage bootstrap/cache \
 && chown -R nobody:nobody storage bootstrap/cache public

# ── Entrypoint ───────────────────────────────────────────────
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

# Render injects PORT at runtime (default 10000)
# We expose 10000 as documentation; actual port comes from $PORT env var
EXPOSE 10000

ENTRYPOINT ["entrypoint.sh"]
