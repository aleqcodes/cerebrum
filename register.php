<?php
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/auth.php';

$mensaje = isset($_GET['mensaje']) ? htmlspecialchars($_GET['mensaje']) : '';
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Crear cuenta - GTD</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-gray-100 flex items-center justify-center p-4">
  <div class="w-full max-w-md bg-white shadow rounded-lg p-6">
    <h1 class="text-2xl font-bold mb-4 text-gray-800">Crear cuenta</h1>
    <?php if ($mensaje): ?>
      <div class="mb-4 p-3 rounded bg-red-50 text-red-600 text-sm"><?php echo $mensaje; ?></div>
    <?php endif; ?>
    <form action="procesar.php" method="POST" class="space-y-4">
      <input type="hidden" name="accion" value="registrar_usuario" />
      <div>
        <label for="nombre" class="block text-sm font-medium text-gray-700">Nombre</label>
        <input type="text" id="nombre" name="nombre" required class="mt-1 w-full border rounded px-3 py-2 focus:outline-none focus:ring focus:border-blue-400" />
      </div>
      <div>
        <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
        <input type="email" id="email" name="email" required class="mt-1 w-full border rounded px-3 py-2 focus:outline-none focus:ring focus:border-blue-400" />
      </div>
      <div>
        <label for="password" class="block text-sm font-medium text-gray-700">Contraseña</label>
        <input type="password" id="password" name="password" required class="mt-1 w-full border rounded px-3 py-2 focus:outline-none focus:ring focus:border-blue-400" />
      </div>
      <div>
        <label for="password2" class="block text-sm font-medium text-gray-700">Confirmar contraseña</label>
        <input type="password" id="password2" name="password2" required class="mt-1 w-full border rounded px-3 py-2 focus:outline-none focus:ring focus:border-blue-400" />
      </div>
      <button type="submit" class="w-full py-2 px-3 rounded bg-blue-600 text-white hover:bg-blue-700">Crear usuario</button>
    </form>
  </div>
</body>
</html>