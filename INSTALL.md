# Guía de Instalación Rápida

## 1. Clonar el proyecto

```bash
cd SISTEMA-FAC
```

## 2. Configurar el entorno

```bash
cp .env.example .env
```

## 3. Instalar dependencias

```bash
docker-compose up -d
docker-compose exec app composer install --no-interaction
```

## 4. Configurar Laravel

```bash
docker-compose exec app php artisan key:generate
docker-compose exec app php artisan storage:link
```

## 5. Base de datos

```bash
docker-compose exec app php artisan migrate --seed
```

## 6. Acceder

URL: **http://localhost:8080**

### Usuarios de prueba:
- **Admin**: `admin@facturacion.local` / `admin123`
- **Cajera**: `cajera@facturacion.local` / `cajera123`
- **Contador**: `contador@facturacion.local` / `contador123`

## 7. Configurar SUNAT (cuando esté listo)

1. Inicia sesión como administrador
2. Ve a **SUNAT → Configuración**
3. Actualiza las credenciales SOL y sube el certificado
4. Activa "Envío automático"

## 8. Configurar empresa

1. Ve a **Configuración**
2. Actualiza datos de tu empresa (RUC, razón social, dirección)
3. Verifica que las series de comprobantes existan (B001, F001)

## 9. Agregar productos y clientes

1. **Productos** → Nuevo producto (con código, precio, categoría)
2. **Clientes** → Agregar clientes frecuentes

## 10. Probar una venta

1. Inicia sesión como cajera
2. **Caja** → Aperturar con monto inicial (S/ 100)
3. **POS** → Agrega productos al carrito
4. F2 → Procesar pago
5. Se genera boleta y se imprime ticket

¡Listo para usar! 🎉
