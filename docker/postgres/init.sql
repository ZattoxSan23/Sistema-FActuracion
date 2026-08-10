-- Configuración inicial de PostgreSQL
-- La base de datos y usuario se crean automáticamente vía variables de entorno

-- Crear extensión unaccent para búsquedas
CREATE EXTENSION IF NOT EXISTS unaccent;

-- Crear extensión pgcrypto para funciones criptográficas
CREATE EXTENSION IF NOT EXISTS pgcrypto;

-- Comentario inicial
COMMENT ON DATABASE facturacion IS 'Sistema de Facturación Electrónica - SUNAT';
