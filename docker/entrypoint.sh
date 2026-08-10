#!/bin/sh
set -e

echo "=== Iniciando Sistema de Facturación ==="

# Generar APP_KEY si no existe
if [ -z "$APP_KEY" ] || [ "$APP_KEY" = "" ]; then
    echo "APP_KEY no está configurada, generando una nueva..."
    APP_KEY="base64:$(php -r 'echo base64_encode(random_bytes(32));')"
    export APP_KEY
    echo "APP_KEY generada correctamente."
fi

# Esperar a que PostgreSQL esté listo
echo "Esperando PostgreSQL..."
i=0
until php -r "
try {
    new PDO('pgsql:host='.getenv('DB_HOST').';port='.getenv('DB_PORT').';dbname='.getenv('DB_DATABASE'), getenv('DB_USERNAME'), getenv('DB_PASSWORD'));
    exit(0);
} catch (Exception \$e) {
    exit(1);
}
" 2>/dev/null; do
    i=$((i+1))
    if [ $i -gt 60 ]; then
        echo "ERROR: PostgreSQL no responde tras 120 segundos."
        exit 1
    fi
    sleep 2
done
echo "PostgreSQL listo."

# Ejecutar migraciones
echo "Ejecutando migraciones..."
php artisan migrate --force --no-interaction 2>&1 | head -20 || echo "Migraciones completadas"

# Crear enlace de storage
php artisan storage:link 2>/dev/null || true

# Optimizar Laravel
php artisan config:cache 2>&1 | tail -3
php artisan route:cache 2>&1 | tail -3
php artisan view:cache 2>&1 | tail -3

echo "=== Aplicación lista ==="

# Iniciar supervisord
exec /usr/bin/supervisord -c /etc/supervisord.conf
