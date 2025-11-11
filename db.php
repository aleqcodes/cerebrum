<?php
// Sesión
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// Configuración de la base de datos
$host = 'localhost';
$dbname = 'gtd_productividad';
$username = 'root';
$password = 'root';

// Configuración de opciones PDO
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
];

try {
    // Crear conexión PDO
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password, $options);
} catch (PDOException $e) {
    // Manejo de errores de conexión
    die("Error de conexión a la base de datos: " . $e->getMessage());
}

// Función auxiliar para obtener todas las tareas
function obtenerTareas($pdo, $condicion = '', $parametros = []) {
    $userId = $_SESSION['user_id'] ?? null;
    $sql = "SELECT t.*, p.nombre as proyecto_nombre 
            FROM tareas t 
            LEFT JOIN proyectos p ON t.id_proyecto = p.id";

    // Filtrar por usuario propietario
    $sql .= " WHERE t.usuario_id = ?";
    if ($condicion) {
        $sql .= " AND $condicion";
    }

    $sql .= " ORDER BY t.fecha_creacion DESC";

    $stmt = $pdo->prepare($sql);
    $stmt->execute(array_merge([$userId], $parametros));
    return $stmt->fetchAll();
}

// Función auxiliar para obtener todos los proyectos
function obtenerProyectos($pdo) {
    $userId = $_SESSION['user_id'] ?? null;
    $stmt = $pdo->prepare("SELECT * FROM proyectos WHERE usuario_id = ? ORDER BY nombre");
    $stmt->execute([$userId]);
    return $stmt->fetchAll();
}

// Función auxiliar para obtener contextos únicos
function obtenerContextos($pdo) {
    $userId = $_SESSION['user_id'] ?? null;
    $stmt = $pdo->prepare("SELECT DISTINCT contexto FROM tareas WHERE usuario_id = ? AND contexto IS NOT NULL AND contexto != '' ORDER BY contexto");
    $stmt->execute([$userId]);
    return $stmt->fetchAll(PDO::FETCH_COLUMN);
}

// Función auxiliar para obtener una tarea por ID
function obtenerTareaPorId($pdo, $id) {
    $userId = $_SESSION['user_id'] ?? null;
    $stmt = $pdo->prepare("SELECT * FROM tareas WHERE id = ? AND usuario_id = ?");
    $stmt->execute([$id, $userId]);
    return $stmt->fetch();
}

// Función auxiliar para obtener un proyecto por ID
function obtenerProyectoPorId($pdo, $id) {
    $userId = $_SESSION['user_id'] ?? null;
    $stmt = $pdo->prepare("SELECT * FROM proyectos WHERE id = ? AND usuario_id = ?");
    $stmt->execute([$id, $userId]);
    return $stmt->fetch();
}

?>