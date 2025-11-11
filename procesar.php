<?php
require_once 'db.php';
require_once 'auth.php';

// Función para redirigir con mensaje
function definirRedireccion($mensaje = '', $tipo = 'success') {
    $url = 'index.php';
    if (isset($_SERVER['HTTP_REFERER'])) {
        $url = $_SERVER['HTTP_REFERER'];
    }
    
    if ($mensaje) {
        $url .= (strpos($url, '?') !== false ? '&' : '?') . 'mensaje=' . urlencode($mensaje) . '&tipo=' . $tipo;
    }
    
    header("Location: $url");
    exit();
}

// Verificar que se recibió una acción
if (!isset($_POST['accion'])) {
    definirRedireccion('No se especificó ninguna acción', 'error');
}

$accion = $_POST['accion'];

// Proteger acciones: solo permitir login y registrar_usuario sin sesión
$accionesPublicas = ['login', 'registrar_usuario'];
if (!in_array($accion, $accionesPublicas)) {
    if (empty($_SESSION['user_id'])) {
        header('Location: login.php?mensaje=' . urlencode('Inicia sesión para continuar'));
        exit();
    }
}

// Evitar registro cuando ya existen usuarios
if ($accion === 'registrar_usuario' && hayUsuarios($pdo)) {
    header('Location: login.php?mensaje=' . urlencode('Ya existe un usuario. Inicia sesión.'));
    exit();
}

try {
    switch ($accion) {
        case 'login':
            if (!hayUsuarios($pdo)) {
                header('Location: register.php');
                exit();
            }
            $email = isset($_POST['email']) ? trim($_POST['email']) : '';
            $password = $_POST['password'] ?? '';
            if ($email === '' || $password === '') {
                header('Location: login.php?mensaje=' . urlencode('Completa email y contraseña'));
                exit();
            }
            $stmt = $pdo->prepare('SELECT id, nombre, email, password_hash FROM usuarios WHERE email = ?');
            $stmt->execute([$email]);
            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($usuario && password_verify($password, $usuario['password_hash'])) {
                $_SESSION['user_id'] = $usuario['id'];
                $_SESSION['user_nombre'] = $usuario['nombre'];
                $_SESSION['user_email'] = $usuario['email'];
                header('Location: index.php');
                exit();
            }
            header('Location: login.php?mensaje=' . urlencode('Credenciales inválidas'));
            exit();

        case 'registrar_usuario':
            $nombre = isset($_POST['nombre']) ? trim($_POST['nombre']) : '';
            $email = isset($_POST['email']) ? trim($_POST['email']) : '';
            $password = $_POST['password'] ?? '';
            $password2 = $_POST['password2'] ?? '';
            if ($nombre === '' || $email === '' || $password === '' || $password2 === '') {
                header('Location: register.php?mensaje=' . urlencode('Todos los campos son obligatorios'));
                exit();
            }
            if ($password !== $password2) {
                header('Location: register.php?mensaje=' . urlencode('Las contraseñas no coinciden'));
                exit();
            }
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare('INSERT INTO usuarios (nombre, email, password_hash) VALUES (?, ?, ?)');
            try {
                $stmt->execute([$nombre, $email, $hash]);
            } catch (PDOException $e) {
                header('Location: register.php?mensaje=' . urlencode('Error al crear usuario: ' . $e->getMessage()));
                exit();
            }
            $id = (int)$pdo->lastInsertId();
            $_SESSION['user_id'] = $id;
            $_SESSION['user_nombre'] = $nombre;
            $_SESSION['user_email'] = $email;
            header('Location: index.php');
            exit();

        case 'logout':
            $_SESSION = [];
            if (session_status() === PHP_SESSION_ACTIVE) {
                session_unset();
                session_destroy();
            }
            header('Location: login.php');
            exit();
        case 'crear_tarea_rapida':
            // Crear tarea rápida (solo título)
            if (empty($_POST['titulo'])) {
                definirRedireccion('El título es requerido', 'error');
            }
            
            $titulo = trim($_POST['titulo']);
            
            $stmt = $pdo->prepare("INSERT INTO tareas (titulo) VALUES (?)");
            $stmt->execute([$titulo]);
            
            definirRedireccion('Tarea añadida a la Bandeja de Entrada', 'success');
            break;
            
        case 'crear_tarea_completa':
            // Crear tarea completa con todos los campos
            if (empty($_POST['titulo'])) {
                definirRedireccion('El título es requerido', 'error');
            }
            
            $titulo = trim($_POST['titulo']);
            $id_proyecto = !empty($_POST['id_proyecto']) ? $_POST['id_proyecto'] : null;
            $fecha_vencimiento = !empty($_POST['fecha_vencimiento']) ? $_POST['fecha_vencimiento'] : null;
            $contexto = !empty($_POST['contexto']) ? trim($_POST['contexto']) : null;
            $descripcion = !empty($_POST['descripcion']) ? trim($_POST['descripcion']) : null;
            
            $stmt = $pdo->prepare("INSERT INTO tareas (titulo, id_proyecto, fecha_vencimiento, contexto, descripcion) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$titulo, $id_proyecto, $fecha_vencimiento, $contexto, $descripcion]);
            
            definirRedireccion('Tarea creada exitosamente', 'success');
            break;
            
        case 'actualizar_tarea':
            // Actualizar tarea existente
            if (empty($_POST['id']) || empty($_POST['titulo'])) {
                definirRedireccion('ID y título son requeridos', 'error');
            }
            
            $id = $_POST['id'];
            $titulo = trim($_POST['titulo']);
            $id_proyecto = !empty($_POST['id_proyecto']) ? $_POST['id_proyecto'] : null;
            $fecha_vencimiento = !empty($_POST['fecha_vencimiento']) ? $_POST['fecha_vencimiento'] : null;
            $contexto = !empty($_POST['contexto']) ? trim($_POST['contexto']) : null;
            $descripcion = !empty($_POST['descripcion']) ? trim($_POST['descripcion']) : null;
            $estado = $_POST['estado'];
            
            // Validar estado
            $estados_validos = ['pendiente', 'en_espera', 'completada'];
            if (!in_array($estado, $estados_validos)) {
                $estado = 'pendiente';
            }
            
            $stmt = $pdo->prepare("UPDATE tareas SET titulo = ?, id_proyecto = ?, fecha_vencimiento = ?, contexto = ?, descripcion = ?, estado = ? WHERE id = ?");
            $stmt->execute([$titulo, $id_proyecto, $fecha_vencimiento, $contexto, $descripcion, $estado, $id]);
            
            definirRedireccion('Tarea actualizada exitosamente', 'success');
            break;
            
        case 'cambiar_estado':
            // Cambiar estado de tarea (para el botón de check)
            if (empty($_POST['id']) || empty($_POST['estado_actual'])) {
                definirRedireccion('ID y estado actual son requeridos', 'error');
            }
            
            $id = $_POST['id'];
            $estado_actual = $_POST['estado_actual'];
            
            // Alternar entre pendiente y completada
            $nuevo_estado = ($estado_actual == 'completada') ? 'pendiente' : 'completada';
            
            $stmt = $pdo->prepare("UPDATE tareas SET estado = ? WHERE id = ?");
            $stmt->execute([$nuevo_estado, $id]);
            
            definirRedireccion('', 'success'); // Sin mensaje para no interrumpir la experiencia
            break;
            
        case 'eliminar_tarea':
            // Eliminar tarea
            if (empty($_POST['id'])) {
                definirRedireccion('ID es requerido', 'error');
            }
            
            $id = $_POST['id'];
            
            $stmt = $pdo->prepare("DELETE FROM tareas WHERE id = ?");
            $stmt->execute([$id]);
            
            definirRedireccion('Tarea eliminada exitosamente', 'success');
            break;
            
        case 'crear_proyecto':
            // Crear nuevo proyecto
            if (empty($_POST['nombre'])) {
                definirRedireccion('El nombre del proyecto es requerido', 'error');
            }
            
            $nombre = trim($_POST['nombre']);
            $descripcion = !empty($_POST['descripcion']) ? trim($_POST['descripcion']) : null;
            
            $stmt = $pdo->prepare("INSERT INTO proyectos (nombre, descripcion) VALUES (?, ?)");
            $stmt->execute([$nombre, $descripcion]);
            
            definirRedireccion('Proyecto creado exitosamente', 'success');
            break;
            
        case 'actualizar_proyecto':
            // Actualizar proyecto existente
            if (empty($_POST['id']) || empty($_POST['nombre'])) {
                definirRedireccion('ID y nombre son requeridos', 'error');
            }
            
            $id = $_POST['id'];
            $nombre = trim($_POST['nombre']);
            $descripcion = !empty($_POST['descripcion']) ? trim($_POST['descripcion']) : null;
            
            $stmt = $pdo->prepare("UPDATE proyectos SET nombre = ?, descripcion = ? WHERE id = ?");
            $stmt->execute([$nombre, $descripcion, $id]);
            
            definirRedireccion('Proyecto actualizado exitosamente', 'success');
            break;
            
        case 'eliminar_proyecto':
            // Eliminar proyecto
            if (empty($_POST['id'])) {
                definirRedireccion('ID es requerido', 'error');
            }
            
            $id = $_POST['id'];
            // Eliminar proyecto (las tareas asociadas quedarán en la Bandeja por ON DELETE SET NULL)
            $stmt = $pdo->prepare("DELETE FROM proyectos WHERE id = ?");
            $stmt->execute([$id]);
            
            definirRedireccion('Proyecto eliminado exitosamente', 'success');
            break;
            
        default:
            definirRedireccion('Acción no válida', 'error');
            break;
    }
    
} catch (PDOException $e) {
    definirRedireccion('Error en la base de datos: ' . $e->getMessage(), 'error');
} catch (Exception $e) {
    definirRedireccion('Error: ' . $e->getMessage(), 'error');
}
?>