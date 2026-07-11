# ---------------------------
# Build Stage
# ---------------------------
FROM dunglas/frankenphp:1.9-builder-php8.4-alpine AS builder

WORKDIR /app

# Install PHP extensions and system dependencies in one layer,
# using --no-install-recommends and cleaning apt caches.
RUN install-php-extensions \
        pcntl \
        pdo_mysql \
        bcmath \
        opcache \
        intl \
        exif \
        fileinfo \
        gd \
        zip \
        redis \
    && apk add --no-cache \
        git \
        curl \
        unzip \
        bash

# Install Bun (latest version)
RUN curl -fsSL https://bun.sh/install | bash
ENV PATH="/root/.bun/bin:$PATH"

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Allow Composer to run as root in containers
ENV COMPOSER_ALLOW_SUPERUSER=1

# --- Caching: PHP dependencies ---
# Copy composer manifests first
COPY composer.json composer.lock ./
RUN composer install \
    --no-dev \
    --prefer-dist \
    --no-interaction \
    --no-progress \
    --optimize-autoloader \
    --no-scripts

# --- Caching: JS dependencies ---
# Copy JS manifests next
COPY package.json bun.lock ./
# Use frozen lockfile when valid; fall back to updating lock if needed
RUN bun install

# Now copy the rest of the application
COPY . .

# Ensure autoload is optimized after all sources are present (without running scripts)
RUN composer dump-autoload --optimize --no-interaction --no-scripts

# Build frontend assets only (avoid baking secrets)
RUN bun x --bun vite build

# Copy Supervisor configuration and entrypoint script
COPY servers/*.conf /etc/supervisor/conf.d/
COPY servers/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

# ---------------------------
# Final Stage
# ---------------------------
FROM dunglas/frankenphp:php8.4-alpine

WORKDIR /app

# Install runtime dependency for netcat
RUN install-php-extensions pcntl \
    pdo_mysql \
    bcmath \
    opcache \
    intl \
    exif \
    fileinfo \
    gd \
    zip \
    redis \
    && apk add --no-cache supervisor netcat-openbsd

# Copy only the built application and necessary configuration from the builder stage
# Dependencies and application code
COPY --from=builder /app/vendor /app/vendor
COPY --from=builder /app/app /app/app
COPY --from=builder /app/bootstrap /app/bootstrap
COPY --from=builder /app/config /app/config
COPY --from=builder /app/database /app/database
COPY --from=builder /app/resources /app/resources
COPY --from=builder /app/storage/framework /app/storage/framework
COPY --from=builder /app/storage/logs /app/storage/logs
COPY --from=builder /app/routes /app/routes
COPY --from=builder /app/artisan /app/artisan
COPY --from=builder /app/composer.json /app/composer.json
COPY --from=builder /app/composer.lock /app/composer.lock
COPY --from=builder /app/.env.example /app/.env.example
COPY --from=builder /app/php.ini /app/php.ini
# Public assets only (includes built assets from Vite)
COPY --from=builder /app/public /app/public
COPY --from=builder /etc/supervisor/conf.d/ /etc/supervisor/conf.d/
COPY --from=builder /usr/local/bin/entrypoint.sh /usr/local/bin/entrypoint.sh

EXPOSE 80 8000 443
ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
    
