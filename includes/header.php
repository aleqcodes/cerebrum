<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GTD Productividad</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-50">
    <!-- Formulario de Captura Rápida -->
    <div class="bg-white shadow-md border-b sticky top-0 z-30">
        <div class="max-w-7xl mx-auto px-2 sm:px-4 lg:px-8">
            <div class="py-3 sm:py-4">
                <!-- Encabezado móvil -->
                <div class="flex items-center justify-between sm:hidden mb-2">
                    <div class="text-lg font-bold text-gray-900">Get the shit Done</div>
                    <button type="button" onclick="toggleSidebar()" class="inline-flex items-center px-3 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <i class="fas fa-bars"></i>
                    </button>
                </div>
                <form action="procesar.php" method="POST" class="flex flex-col sm:flex-row gap-3 sm:gap-4 items-stretch sm:items-end">
                    <input type="hidden" name="accion" value="crear_tarea_rapida">
                    <div class="flex-1">
                        <label for="titulo_rapido" class="block text-sm font-medium text-gray-700 mb-1">
                            <i class="fas fa-plus-circle mr-1"></i>Captura Rápida
                        </label>
                        <input type="text" 
                               name="titulo" 
                               id="titulo_rapido" 
                               placeholder="¿Qué necesitas recordar?"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               required>
                    </div>
                    <button type="submit" 
                            class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 w-full sm:w-auto">
                        <i class="fas fa-paper-plane mr-1"></i>Añadir
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Overlay para menú lateral en móvil -->
    <div id="sidebarOverlay" class="fixed inset-0 bg-black bg-opacity-30 hidden z-40 md:hidden" onclick="toggleSidebar()"></div>

    <div class="flex min-h-screen pt-0">
        <!-- Menú Lateral -->
        <div id="sidebar" class="fixed md:relative top-0 left-0 h-full md:h-auto w-64 bg-white shadow-lg z-50 transform -translate-x-full md:translate-x-0 transition-transform">
            <div class="p-6">
                <h1 class="text-2xl font-bold text-gray-800 mb-6 flex items-center justify-between">
                    <i class="fas fa-tasks mr-2"></i>GTD
                    <button type="button" class="md:hidden inline-flex items-center px-2 py-1 text-gray-600 hover:text-gray-900" onclick="toggleSidebar()">
                        <i class="fas fa-times"></i>
                    </button>
                    <a href="#" class="hidden md:inline-flex items-center text-sm text-gray-600 hover:text-red-600" onclick="document.getElementById('logoutForm').submit();">
                        <i class="fas fa-right-from-bracket mr-2"></i>Salir
                    </a>
                    <form id="logoutForm" action="procesar.php" method="POST" class="hidden">
                        <input type="hidden" name="accion" value="logout">
                    </form>
                </h1>
                
                <nav class="space-y-2">
                    <a href="index.php?vista=bandeja" 
                       class="flex items-center px-4 py-2 text-gray-700 rounded-md hover:bg-gray-100 <?php echo (!isset($_GET['vista']) || $_GET['vista'] == 'bandeja') ? 'bg-blue-100 text-blue-700' : ''; ?>">
                        <i class="fas fa-inbox mr-3"></i>
                        Bandeja de Entrada
                    </a>
                    
                    <a href="index.php?vista=hoy" 
                       class="flex items-center px-4 py-2 text-gray-700 rounded-md hover:bg-gray-100 <?php echo (isset($_GET['vista']) && $_GET['vista'] == 'hoy') ? 'bg-blue-100 text-blue-700' : ''; ?>">
                        <i class="fas fa-calendar-day mr-3"></i>
                        Hoy
                    </a>
                    
                    <a href="index.php?vista=proyectos" 
                       class="flex items-center px-4 py-2 text-gray-700 rounded-md hover:bg-gray-100 <?php echo (isset($_GET['vista']) && $_GET['vista'] == 'proyectos') ? 'bg-blue-100 text-blue-700' : ''; ?>">
                        <i class="fas fa-folder mr-3"></i>
                        Proyectos
                    </a>
                    
                    <a href="index.php?vista=contextos" 
                       class="flex items-center px-4 py-2 text-gray-700 rounded-md hover:bg-gray-100 <?php echo (isset($_GET['vista']) && $_GET['vista'] == 'contextos') ? 'bg-blue-100 text-blue-700' : ''; ?>">
                        <i class="fas fa-tags mr-3"></i>
                        Contextos
                    </a>
                    
                    <a href="index.php?vista=espera" 
                       class="flex items-center px-4 py-2 text-gray-700 rounded-md hover:bg-gray-100 <?php echo (isset($_GET['vista']) && $_GET['vista'] == 'espera') ? 'bg-blue-100 text-blue-700' : ''; ?>">
                        <i class="fas fa-pause-circle mr-3"></i>
                        En Espera
                    </a>

                    <a href="index.php?vista=vencidas" 
                       class="flex items-center px-4 py-2 text-gray-700 rounded-md hover:bg-gray-100 <?php echo (isset($_GET['vista']) && $_GET['vista'] == 'vencidas') ? 'bg-blue-100 text-blue-700' : ''; ?>">
                        <i class="fas fa-triangle-exclamation mr-3"></i>
                        Vencidas
                    </a>
                </nav>
            </div>
        </div>

        <!-- Contenido Principal -->
        <div class="flex-1 overflow-y-auto">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8">