#!/bin/bash
set -e

# ──────────────────────────────────────────────
# ENTRYPOINT PARA CONTENEDORES LARAVEL
# Ejecuta: composer install, .env copy, key:generate, migrate
# ──────────────────────────────────────────────

echo "🚀 Iniciando entrypoint Laravel..."

# 1. Copiar .env si no existe
if [ ! -f .env ]; then
    echo "📋 Copiando .env.example → .env"
    cp .env.example .env
fi

# 2. Instalar dependencias Composer (solo si vendor no existe o composer.lock cambió)
if [ ! -d vendor ] || [ composer.lock -nt vendor/composer/installed.json ]; then
    echo "📦 Instalando dependencias Composer..."
    composer install --no-interaction --prefer-dist --optimize-autoloader
fi

# 3. Generar APP_KEY si está vacío
if [ -z "$(grep '^APP_KEY=' .env | cut -d'=' -f2)" ]; then
    echo "🔑 Generando APP_KEY..."
    php artisan key:generate --force
fi

# 4. Esperar a que MySQL esté listo (máx 60s)
echo "⏳ Esperando a MySQL..."
for i in {1..30}; do
    if php -r "new PDO('mysql:host=mysql;dbname=live_tracking', 'root', 'root');" 2>/dev/null; then
        echo "✅ MySQL disponible"
        break
    fi
    sleep 2
done

# 5. Ejecutar migraciones (solo si RUN_MIGRATIONS no es "false")
if [ "${RUN_MIGRATIONS:-true}" != "false" ]; then
    echo "🗄️  Ejecutando migraciones..."
    php artisan migrate --force --no-interaction
else
    echo "⏭️  Saltando migraciones (RUN_MIGRATIONS=false)"
fi

# 6. Limpiar y cachear config
echo "⚡ Optimizando Laravel..."
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "✅ Entrypoint completado. Iniciando supervisord..."

# Ejecutar comando pasado (supervisord)
exec "$@"