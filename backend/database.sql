-- Base de datos para Schweizer Inmobiliaria
CREATE DATABASE IF NOT EXISTS schweizer_inmobiliaria CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE schweizer_inmobiliaria;

-- Tabla de propiedades
CREATE TABLE IF NOT EXISTS propiedades (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(255) NOT NULL,
    descripcion TEXT,
    tipo ENUM('venta', 'alquiler') NOT NULL,
    precio DECIMAL(12, 2) NOT NULL,
    ubicacion VARCHAR(255) NOT NULL,
    dormitorios INT NOT NULL,
    banos INT NOT NULL,
    superficie DECIMAL(10, 2) NOT NULL,
    destacada BOOLEAN DEFAULT FALSE,
    activa BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_tipo (tipo),
    INDEX idx_activa (activa),
    INDEX idx_destacada (destacada)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de imágenes
CREATE TABLE IF NOT EXISTS imagenes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    propiedad_id INT NOT NULL,
    filename VARCHAR(255) NOT NULL,
    orden INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (propiedad_id) REFERENCES propiedades(id) ON DELETE CASCADE,
    INDEX idx_propiedad_id (propiedad_id),
    INDEX idx_orden (orden)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de sesiones de admin (opcional, para persistencia)
CREATE TABLE IF NOT EXISTS admin_sessions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    session_token VARCHAR(255) NOT NULL UNIQUE,
    username VARCHAR(100) NOT NULL,
    expires_at TIMESTAMP NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_token (session_token),
    INDEX idx_expires (expires_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insertar propiedades de ejemplo (opcional)
INSERT INTO propiedades (titulo, descripcion, tipo, precio, ubicacion, dormitorios, banos, superficie, destacada) VALUES
('Departamento Moderno en Centro', 'Hermoso departamento de 2 dormitorios en el corazón de Mendoza', 'venta', 95000, 'Centro, Mendoza', 2, 2, 85, true),
('Casa Familiar en Godoy Cruz', 'Amplia casa de 3 dormitorios con patio y quincho', 'venta', 150000, 'Godoy Cruz, Mendoza', 3, 2, 180, false),
('Departamento en Alquiler', 'Acogedor departamento de 1 dormitorio amoblado', 'alquiler', 450, 'Guaymallén, Mendoza', 1, 1, 45, false),
('Casa de Lujo en Country', 'Exclusiva propiedad con pileta y amplios jardines', 'alquiler', 1200, 'Chacras de Coria, Mendoza', 4, 3, 350, true);
