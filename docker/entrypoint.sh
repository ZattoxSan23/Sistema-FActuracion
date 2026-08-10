#!/bin/sh
set -e

echo "=== Iniciando Sistema de Facturación ==="

# Generar APP_KEY si no existe
if [ -z "$APP_KEY" ]; then
    echo "ERROR: APP_KEY no está configurada. Agregala en las variables de entorno de Render."
    exit 1
fi

# Esperar a que PostgreSQL esté listo
echo "Esperando PostgreSQL..."
until php -r "new PDO('pgsql:host='.getenv('DB_HOST').';port='.getenv('DB_PORT').';dbname='.getenv('DB_DATABASE'), getenv('DB_USERNAME'), getenv('DB_PASSWORD'));" 2>/dev/null; do
    sleep 2
done
echo "PostgreSQL listo."

# Ejecutar migraciones
echo "Ejecutando migraciones..."
php artisan migrate --force --no-interaction || echo "Migraciones fallaron (puede ser normal si ya están aplicadas)"

# Crear enlace de storage
php artisan storage:link 2>/dev/null || true

# Optimizar Laravel
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "=== Aplicación lista ==="

# Iniciar supervisord
exec /usr/bin/supervisord -c /etc/supervisord.conf
