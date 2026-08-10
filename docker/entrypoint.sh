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

echo "DB_HOST=$DB_HOST"
echo "DB_PORT=$DB_PORT"
echo "DB_DATABASE=$DB_DATABASE"
echo "DB_USERNAME=$DB_USERNAME"

# Crear .env temporal con las variables de Render
cat > /var/www/html/.env <<EOF
APP_NAME="Sistema de Facturación"
APP_ENV=production
APP_KEY=${APP_KEY}
APP_DEBUG=false
APP_URL=https://${RENDER_EXTERNAL_HOSTNAME:-localhost}

LOG_CHANNEL=stderr

DB_CONNECTION=pgsql
DB_HOST=${DB_HOST}
DB_PORT=${DB_PORT}
DB_DATABASE=${DB_DATABASE}
DB_USERNAME=${DB_USERNAME}
DB_PASSWORD=${DB_PASSWORD}

CACHE_DRIVER=database
SESSION_DRIVER=database
QUEUE_CONNECTION=sync
FILESYSTEM_DISK=local

SUNAT_ENV=beta
SUNAT_GRE_URL=https://gre-test.nubefact.com/ol-ti-itcpe/billService
SUNAT_OSE_URL=https://ose-test.nubefact.com/api/v1
SUNAT_RUC=20000000001
SUNAT_RAZON_SOCIAL="EMPRESA DE PRUEBA S.A.C."
SUNAT_NOMBRE_COMERCIAL="MI TIENDA"

DOCS_API_URL=https://api.decolecta.com/v1
DOCS_API_KEY=
DOCS_API_TIMEOUT=10

APP_TIMEZONE=America/Lima
APP_LOCALE=es
APP_FALLBACK_LOCALE=es
APP_FAKER_LOCALE=es_PE
EOF

echo "Archivo .env creado."

# Esperar a que PostgreSQL esté listo
echo "Esperando PostgreSQL en ${DB_HOST}:${DB_PORT}..."
i=0
until php -r "
\$dsn = 'pgsql:host='.getenv('DB_HOST').';port='.getenv('DB_PORT').';dbname='.getenv('DB_DATABASE');
try {
    new PDO(\$dsn, getenv('DB_USERNAME'), getenv('DB_PASSWORD'));
    exit(0);
} catch (Exception \$e) {
    exit(1);
}
" 2>&1; do
    i=$((i+1))
    if [ $i -gt 30 ]; then
        echo "ERROR: PostgreSQL no responde."
        exit 1
    fi
    sleep 3
done
echo "PostgreSQL listo."

# Ejecutar migraciones
echo "Ejecutando migraciones..."
php artisan migrate --force --no-interaction 2>&1 | tail -10

# Ejecutar seeders (solo si las tablas están vacías)
echo "Ejecutando seeders..."
php artisan db:seed --force --no-interaction 2>&1 | tail -20

# Crear enlace de storage
php artisan storage:link 2>/dev/null || true

# Optimizar
php artisan config:cache 2>&1 | tail -3
php artisan route:cache 2>&1 | tail -3
php artisan view:cache 2>&1 | tail -3

echo "=== Aplicación lista ==="

# Iniciar supervisord
exec /usr/bin/supervisord -c /etc/supervisord.conf
