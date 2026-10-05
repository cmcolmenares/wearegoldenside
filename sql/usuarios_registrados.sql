-- GoldenSide — tabla de registros del formulario /Comunidad
-- Ejecutar contra el MySQL incluido en el hosting compartido de IONOS
-- (Panel de IONOS -> Hosting -> Bases de datos -> phpMyAdmin / consola SQL)
-- antes del primer registro.

CREATE TABLE IF NOT EXISTS usuarios_registrados (
    id              BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombre_completo VARCHAR(100) NOT NULL,
    correo          VARCHAR(255) NOT NULL,
    pais            VARCHAR(100) NULL,         -- reservado para uso futuro
    created_at      TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uq_usuarios_registrados_correo (correo)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
