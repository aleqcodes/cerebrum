-- Base de datos GTD Productividad
CREATE DATABASE IF NOT EXISTS gtd_productividad;
USE gtd_productividad;

--
-- 1. Tabla de usuarios (DEBE CREARSE PRIMERO)
--
CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- 2. Tabla de proyectos (Ahora puede referenciar a usuarios.id)
--
CREATE TABLE IF NOT EXISTS proyectos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT, -- Esta columna debe existir para la FK
    nombre VARCHAR(255) NOT NULL,
    descripcion TEXT,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    -- Definimos el índice y la clave foránea aquí mismo
    INDEX idx_proyectos_usuario (usuario_id),
    CONSTRAINT fk_proyectos_usuario
        FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
        ON DELETE CASCADE -- Si se borra un usuario, se borran sus proyectos
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- 3. Tabla de tareas (Ahora puede referenciar a usuarios.id y proyectos.id)
--
CREATE TABLE IF NOT EXISTS tareas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT, -- Esta columna debe existir para la FK
    titulo VARCHAR(255) NOT NULL,
    id_proyecto INT,
    fecha_vencimiento DATE,
    contexto VARCHAR(100),
    descripcion TEXT,
    estado ENUM('pendiente', 'en_espera', 'completada') DEFAULT 'pendiente',
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    -- Definimos las claves foráneas aquí
    INDEX idx_tareas_usuario (usuario_id),
    CONSTRAINT fk_tareas_usuario
        FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
        ON DELETE CASCADE, -- Si se borra un usuario, se borran sus tareas
    
    CONSTRAINT fk_tareas_proyecto
        FOREIGN KEY (id_proyecto) REFERENCES proyectos(id)
        ON DELETE SET NULL -- Si se borra un proyecto, la tarea queda "sin proyecto"
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- 4. Insertar datos de ejemplo
--

-- Primero, insertamos un usuario de ejemplo (tendrá id = 1)
INSERT INTO usuarios (nombre, email, password_hash) VALUES
('Alejandro', 'test@email.com', 'un_hash_de_ejemplo_123');

-- Ahora, usamos el usuario_id = 1 para los proyectos
INSERT INTO proyectos (usuario_id, nombre, descripcion) VALUES
(1, 'Proyecto Personal', 'Tareas personales y del hogar'),
(1, 'Trabajo', 'Tareas relacionadas con el trabajo'),
(1, 'Estudio', 'Tareas de aprendizaje y formación');

-- Y usamos el usuario_id = 1 para las tareas
-- Nota: Los id_proyecto (2 y 3) funcionarán porque coinciden con los proyectos "Trabajo" y "Estudio"
INSERT INTO tareas (usuario_id, titulo, id_proyecto, fecha_vencimiento, contexto, estado) VALUES
(1, 'Comprar leche', NULL, CURDATE(), '@compras', 'pendiente'),
(1, 'Enviar informe mensual', 2, CURDATE(), '@trabajo', 'pendiente'),
(1, 'Estudiar PHP', 3, DATE_ADD(CURDATE(), INTERVAL 3 DAY), '@focus', 'en_espera'),
(1, 'Llamar al cliente', 2, DATE_ADD(CURDATE(), INTERVAL 1 DAY), '@llamadas', 'pendiente'),
(1, 'Organizar escritorio', 1, NULL, '@casa', 'completada');