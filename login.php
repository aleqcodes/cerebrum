<?php
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/auth.php';

// Si ya hay sesión y usuarios, redirigir al inicio
if (hayUsuarios($pdo) && !empty($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$mensaje = isset($_GET['mensaje']) ? htmlspecialchars($_GET['mensaje']) : '';
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Iniciar sesión - GTD</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-gray-100 flex items-center justify-center p-4">
  <div class="w-full max-w-md bg-white shadow rounded-lg p-6">
    <h1 class="text-2xl font-bold mb-4 text-gray-800">Iniciar sesión</h1>
    <?php if ($mensaje): ?>
      <div class="mb-4 p-3 rounded bg-red-50 text-red-600 text-sm"><?php echo $mensaje; ?></div>
    <?php endif; ?>
    <?php if (!hayUsuarios($pdo)): ?>
      <div class="mb-4 p-3 rounded bg-yellow-50 text-yellow-700 text-sm">
        No hay usuarios creados. Crea el primer usuario para proteger tus tareas.
      </div>
      <a href="register.php" class="inline-flex items-center justify-center w-full py-2 px-3 rounded bg-blue-600 text-white hover:bg-blue-700">Crear cuenta</a>
    <?php else: ?>
      <form action="procesar.php" method="POST" class="space-y-4">
        <input type="hidden" name="accion" value="login" />
        <div>
          <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
          <input type="email" id="email" name="email" required class="mt-1 w-full border rounded px-3 py-2 focus:outline-none focus:ring focus:border-blue-400" />
        </div>
        <div>
          <label for="password" class="block text-sm font-medium text-gray-700">Contraseña</label>
          <input type="password" id="password" name="password" required class="mt-1 w-full border rounded px-3 py-2 focus:outline-none focus:ring focus:border-blue-400" />
        </div>
        <button type="submit" class="w-full py-2 px-3 rounded bg-blue-600 text-white hover:bg-blue-700">Entrar</button>
      </form>
      <div class="mt-4 text-center">
        <a href="register.php" class="text-sm text-blue-600 hover:text-blue-800">Crear cuenta nueva</a>
      </div>
    <?php endif; ?>
  </div>
</body>
</html>