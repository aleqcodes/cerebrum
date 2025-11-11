<?php
require_once __DIR__ . '/db.php';

function hayUsuarios(PDO $pdo): bool {
    try {
        $count = (int)$pdo->query('SELECT COUNT(*) FROM usuarios')->fetchColumn();
        return $count > 0;
    } catch (Throwable $e) {
        return false;
    }
}

function require_login(PDO $pdo): void {
    if (!hayUsuarios($pdo)) {
        header('Location: register.php');
        exit;
    }
    if (empty($_SESSION['user_id'])) {
        header('Location: login.php');
        exit;
    }
}

function usuarioActual(PDO $pdo): ?array {
    if (empty($_SESSION['user_id'])) {
        return null;
    }
    $stmt = $pdo->prepare('SELECT id, nombre, email FROM usuarios WHERE id = ?');
    $stmt->execute([$_SESSION['user_id']]);
    $u = $stmt->fetch(PDO::FETCH_ASSOC);
    return $u ?: null;
}

?>