# Estado de mantenimiento

Este repositorio es un prototipo de demostración. La existencia de módulos SUNAT, XML y POS no acredita cumplimiento tributario ni pruebas de integración contra los servicios reales.

## Arranque Docker

El entrypoint ejecuta migraciones y optimización sin tuberías a `tail`: un fallo ahora detiene el contenedor en vez de quedar oculto por el código de salida del último comando.

Los seeders ya no corren en cada reinicio. Para una base **nueva de demostración**, configura `SEED_DEMO_DATA=true`. Se inicializan solo si no existen usuarios; si ya hay usuarios se omiten. La comprobación evita la repetición habitual al reiniciar, pero no reemplaza un bloqueo de despliegue para arranques simultáneos. Mantén una sola instancia durante la inicialización.

No ejecutes seeders sobre una base de uso real: crean cuentas conocidas y ventas simuladas. No hay que borrar tablas para actualizar la aplicación.

## Configuración que debes revisar

Mantén una `APP_KEY` persistente en el hosting. La generación automática actual cuando falta la variable cambia la clave entre reinicios y puede invalidar cookies o datos cifrados. El servicio también necesita conexiones PostgreSQL válidas, almacenamiento persistente para archivos y configuración SUNAT independiente por entorno.

## Hallazgos de lógica pendientes

En `PosController::storeVenta`, los descuentos de ítem se guardan pero los acumulados de operaciones se calculan desde subtotales previos al descuento. El descuento global se registra sin incorporarse al total. El servidor también necesita comprobar la cobertura de pagos y la disponibilidad de stock bajo concurrencia.

Estos flujos requieren pruebas contables y transaccionales antes de considerarse listos. No se modificaron importes ni comprobantes existentes. Queda definir claramente precios con/sin IGV, redondeos, descuentos, pagos parciales y reglas por tipo de afectación.

## Validación

```sh
sh -n docker/entrypoint.sh
composer validate
```

Después de configurar una base de prueba: probar primer arranque, reinicio sin duplicar seeders, migración fallida y venta con distintos medios de pago. No hay todavía una suite de integración que acredite los módulos descritos abajo.

---

# Sistema de Facturación Electrónica

Prototipo de facturación electrónica con módulos de integración SUNAT para Perú, desarrollado en **Laravel 11 + PostgreSQL + Docker**.

## 🎯 Características

### 3 Roles diferenciados
- **Administrador**: Acceso total al sistema
- **Cajera**: Punto de venta, gestión de caja y clientes
- **Contador**: Reportes contables, libro de ventas, análisis

### Módulos principales
- ✅ **Punto de Venta (POS)**: Interfaz táctil, búsqueda de productos, escaneo de códigos de barras, carrito, múltiples medios de pago
- ✅ **Gestión de Caja**: Apertura/cierre, movimientos, **cuadratura con diferencia**, reporte de cierre
- ✅ **Comprobantes Electrónicos**: Boletas (03), Facturas (01), Notas de Crédito/Débito
- ✅ **Integración SUNAT**: XML UBL 2.1, firma digital XMLDSig, envío a OSE/GRE, recepción de CDR, comunicación de baja
- ✅ **Impresión multi-formato**:
  - Ticket 80mm térmico (ESC/POS)
  - A4 / A5 (PDF con DomPDF)
- ✅ **Reportes contables**: Libro de ventas, ventas por día/mes, top productos, por categoría, por vendedor, por método de pago, IGV
- ✅ **Medios de pago**: Efectivo, Tarjeta (débito/crédito), Yape, Plin, Transferencias
- ✅ **CRUDs completos**: Productos, categorías, clientes, usuarios, series de comprobantes
- ✅ **Multi-sucursal ready** (configuración actual: una caja)

## 🚀 Instalación con Docker

### Requisitos previos
- Docker 24+
- Docker Compose 2+

### Pasos

```bash
# 1. Clonar/copiar el proyecto
cd SISTEMA-FAC

# 2. Copiar variables de entorno
cp .env.example .env

# 3. Levantar contenedores
docker-compose up -d

# 4. Instalar dependencias de PHP
docker-compose exec app composer install

# 5. Generar APP_KEY
docker-compose exec app php artisan key:generate

# 6. Ejecutar migraciones y seeders
docker-compose exec app php artisan migrate --seed

# 7. Crear enlace simbólico de storage
docker-compose exec app php artisan storage:link

# 8. Acceder al sistema
# http://localhost:8080
```

### Credenciales iniciales

| Rol | Email | Contraseña |
|-----|-------|-----------|
| Administrador | `admin@facturacion.local` | `admin123` |
| Cajera | `cajera@facturacion.local` | `cajera123` |
| Contador | `contador@facturacion.local` | `contador123` |

## 📁 Estructura del proyecto

```
SISTEMA-FAC/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Auth/LoginController.php
│   │   │   ├── DashboardController.php
│   │   │   ├── PosController.php
│   │   │   ├── VentaController.php
│   │   │   ├── CajaController.php
│   │   │   ├── ProductoController.php
│   │   │   ├── ClienteController.php
│   │   │   ├── CategoriaController.php
│   │   │   ├── UsuarioController.php
│   │   │   ├── ReporteController.php
│   │   │   ├── SunatController.php
│   │   │   ├── ConfiguracionController.php
│   │   │   └── PerfilController.php
│   │   └── Middleware/
│   │       ├── CheckRole.php
│   │       └── CajaAbierta.php
│   ├── Models/
│   │   ├── User.php, Empresa.php, Cliente.php
│   │   ├── Producto.php, Categoria.php
│   │   ├── Venta.php, VentaItem.php, Pago.php
│   │   ├── Caja.php, CajaMovimiento.php
│   │   ├── Comprobante.php, Serie.php
│   │   ├── SunatConfig.php, SunatRespuesta.php
│   ├── Services/
│   │   ├── Sunat/SunatService.php
│   │   ├── Sunat/XmlGenerator.php
│   │   └── Printer/PrinterService.php
│   ├── Policies/
│   └── Providers/
├── database/
│   ├── migrations/
│   ├── seeders/
│   └── factories/
├── resources/views/
│   ├── auth/login.blade.php
│   ├── layouts/
│   ├── pos/
│   ├── ventas/
│   ├── caja/
│   ├── productos/
│   ├── clientes/
│   ├── categorias/
│   ├── usuarios/
│   ├── reportes/
│   ├── sunat/
│   ├── configuracion/
│   ├── perfil/
│   └── dashboard/
├── routes/
│   └── web.php
├── docker/
│   ├── nginx/conf.d/
│   ├── php/
│   └── postgres/
├── docker-compose.yml
├── Dockerfile
└── .env.example
```

## 🔧 Configuración SUNAT

### Producción
1. Obtener **certificado digital** (extensión `.pfx`) de una entidad autorizada (eFirma, SUNAT, etc.)
2. Colocar el certificado en `storage/app/sunat/certificate.pfx`
3. Actualizar el path en `php artisan tinker`:
   ```php
   $c = \App\Models\SunatConfig::first();
   $c->update([
       'entorno' => 'produccion',
       'usuario_sol' => 'TU_USUARIO_SOL',
       'clave_sol' => 'TU_CLAVE_SOL',
       'certificado_path' => '/var/www/html/storage/app/sunat/certificate.pfx',
       'certificado_password' => 'tu_password',
       'envio_automatico' => true,
   ]);
   ```
4. El sistema firmará XML y enviará a SUNAT automáticamente.

### Beta/Pruebas
- Por defecto el sistema usa el entorno BETA de Nubefact.
- Credenciales SOL demo: `MODDATOS / moddatos`.

## 🖨️ Impresoras

### Linux (80mm térmica)
- Asegurar permisos: `sudo usermod -aG lp www-data`
- Configurar `PRINTER_TICKET_PATH=/dev/usb/lp0`

### Windows
- Instalar la impresora y compartirla
- Configurar `PRINTER_TICKET_NAME` con el nombre exacto de la impresora

### macOS
- Usar CUPS: `PRINTER_TICKET_NAME=NombreImpresora`

## 📊 Flujo de trabajo diario

### Para la Cajera:
1. **Login** → Abre Caja (con monto inicial)
2. **POS** → Agrega productos al carrito
3. **Procesar pago** → Selecciona método (efectivo/tarjeta/Yape/transferencia)
4. **Comprobante** → Boleta/Factura automática → Envío SUNAT
5. **Cerrar Caja** → Cuadra el efectivo real vs teórico

### Para el Contador:
1. Accede a **Reportes**
2. Filtra por fechas
3. Genera **Libro de Ventas** para declaración SUNAT
4. Exporta a PDF

### Para el Administrador:
1. Gestiona productos, clientes, usuarios
2. Configura empresa, series, SUNAT
3. Anula ventas si es necesario
4. Monitorea comprobantes enviados

## 📦 Stack tecnológico

- **Backend**: PHP 8.3, Laravel 11
- **Base de datos**: PostgreSQL 16
- **Cache/Session**: Redis 7
- **Servidor**: Nginx + PHP-FPM
- **Frontend**: Blade + Bootstrap 5 + Chart.js + SweetAlert2
- **PDF**: DomPDF (barryvdh/laravel-dompdf)
- **Impresión térmica**: mike42/escpos-php
- **Firma XML**: robrichards/xmlseclibs
- **HTTP Client**: Guzzle

## 🧪 Comandos útiles

```bash
# Acceder al contenedor de la app
docker-compose exec app bash

# Ejecutar migraciones
php artisan migrate

# Revertir migración
php artisan migrate:rollback

# Refrescar BD con seeders
php artisan migrate:fresh --seed

# Limpiar caché
php artisan cache:clear
php artisan config:clear

# Generar certificado autofirmado para pruebas
openssl req -x509 -newkey rsa:2048 -nodes -sha256 -days 365 \
  -keyout storage/app/sunat/key.pem \
  -out storage/app/sunat/cert.pem \
  -subj "/C=PE/ST=Lima/L=Lima/O=MiTienda/CN=facturacion"
```

## 📜 Licencia

MIT

## 🤝 Soporte

Para soporte, abre un issue en el repositorio o contacta al equipo de desarrollo.
