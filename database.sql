-- Base de datos GTD Productividad
CREATE DATABASE IF NOT EXISTS gtd_productividad;
USE gtd_productividad;

-- Tabla de proyectos
CREATE TABLE IF NOT EXISTS proyectos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(255) NOT NULL,
    descripcion TEXT,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de tareas
CREATE TABLE IF NOT EXISTS tareas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(255) NOT NULL,
    id_proyecto INT,
    fecha_vencimiento DATE,
    contexto VARCHAR(100),
    descripcion TEXT,
    estado ENUM('pendiente', 'en_espera', 'completada') DEFAULT 'pendiente',
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (id_proyecto) REFERENCES proyectos(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insertar datos de ejemplo
INSERT INTO proyectos (nombre, descripcion) VALUES
('Proyecto Personal', 'Tareas personales y del hogar'),
('Trabajo', 'Tareas relacionadas con el trabajo'),
('Estudio', 'Tareas de aprendizaje y formación');

INSERT INTO tareas (titulo, id_proyecto, fecha_vencimiento, contexto, estado) VALUES
('Comprar leche', NULL, CURDATE(), '@compras', 'pendiente'),
('Enviar informe mensual', 2, CURDATE(), '@trabajo', 'pendiente'),
('Estudiar PHP', 3, DATE_ADD(CURDATE(), INTERVAL 3 DAY), '@focus', 'en_espera'),
('Llamar al cliente', 2, DATE_ADD(CURDATE(), INTERVAL 1 DAY), '@llamadas', 'pendiente'),
('Organizar escritorio', 1, NULL, '@casa', 'completada');
-- Tabla de usuarios
CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;