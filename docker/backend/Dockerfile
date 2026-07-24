FROM php:8.4-fpm

# ── Argumentos para cache busting ────────────
ARG UID=1000
ARG GID=1000
ARG SUPERVISOR_CONF=supervisord.conf

# ── Dependencias del sistema ─────────────────
RUN apt-get update && apt-get install -y --no-install-recommends \
        git \
        unzip \
        zip \
        curl \
        libzip-dev \
        libpng-dev \
        libonig-dev \
        libxml2-dev \
        supervisor \
    && docker-php-ext-install -j$(nproc) \
        pdo_mysql \
        mbstring \
        zip \
        exif \
        pcntl \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# ── Composer ─────────────────────────────────
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# ── Usuario no-root ──────────────────────────
RUN groupadd -g ${GID} appuser \
    && useradd -u ${UID} -g ${GID} -m -s /bin/bash appuser \
    && mkdir -p /var/www/html \
    && chown -R appuser:appuser /var/www/html

# ── Directorio de trabajo ────────────────────
WORKDIR /var/www/html

# ── Crear directorio de logs para supervisor ─────────────────
RUN mkdir -p /var/www/html/storage/logs && chown -R appuser:appuser /var/www/html/storage

# ── Entrypoint script ────────────────────────
COPY docker/backend/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

# ── Supervisord config ───────────────────────
COPY docker/backend/${SUPERVISOR_CONF} /etc/supervisor/conf.d/supervisord.conf

USER appuser

ENTRYPOINT ["entrypoint.sh"]
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]