<?php
require_once 'db.php';
require_once 'auth.php';
require_login($pdo);
require_once 'includes/header.php';

// Determinar qué vista mostrar
$vista = isset($_GET['vista']) ? $_GET['vista'] : 'bandeja';

// Función para mostrar el título de la vista actual
function obtenerTituloVista($vista) {
    $titulos = [
        'bandeja' => 'Bandeja de Entrada',
        'hoy' => 'Tareas de Hoy',
        'vencidas' => 'Tareas Vencidas',
        'proyectos' => 'Proyectos',
        'contextos' => 'Contextos',
        'espera' => 'En Espera'
    ];
    return $titulos[$vista] ?? 'Bandeja de Entrada';
}

// Obtener datos según la vista
$tareas = [];
$proyectos = obtenerProyectos($pdo);
$contextos = obtenerContextos($pdo);

switch ($vista) {
    case 'hoy':
        $tareas = obtenerTareas($pdo, 'DATE(t.fecha_vencimiento) = CURDATE()');
        break;
    case 'vencidas':
        $tareas = obtenerTareas($pdo, "t.fecha_vencimiento IS NOT NULL AND DATE(t.fecha_vencimiento) < CURDATE() AND t.estado != 'completada'");
        break;
    case 'proyectos':
        if (isset($_GET['proyecto_id'])) {
            $proyectoId = $_GET['proyecto_id'];
            $tareas = obtenerTareas($pdo, 't.id_proyecto = ?', [$proyectoId]);
            $proyectoActual = obtenerProyectoPorId($pdo, $proyectoId);
        }
        break;
    case 'contextos':
        if (isset($_GET['contexto'])) {
            $contexto = $_GET['contexto'];
            $tareas = obtenerTareas($pdo, 't.contexto = ?', [$contexto]);
        }
        break;
    case 'espera':
        $tareas = obtenerTareas($pdo, "t.estado = 'en_espera'");
        break;
    default: // bandeja
        $tareas = obtenerTareas($pdo, 't.id_proyecto IS NULL');
        break;
}
?>

<!-- Título de la vista -->
<div class="mb-8">
    <h2 class="text-3xl font-bold text-gray-900 mb-2">
        <i class="fas fa-<?php echo $vista == 'bandeja' ? 'inbox' : 
                          ($vista == 'hoy' ? 'calendar-day' : 
                           ($vista == 'proyectos' ? 'folder' : 
                            ($vista == 'contextos' ? 'tags' : 
                             ($vista == 'vencidas' ? 'triangle-exclamation' : 'pause-circle')))); ?> mr-3"></i>
        <?php echo obtenerTituloVista($vista); ?>
    </h2>
    <?php if ($vista == 'proyectos' && isset($proyectoActual)): ?>
        <p class="text-gray-600">Proyecto: <?php echo htmlspecialchars($proyectoActual['nombre']); ?></p>
    <?php elseif ($vista == 'contextos' && isset($contexto)): ?>
        <p class="text-gray-600">Contexto: <?php echo htmlspecialchars($contexto); ?></p>
    <?php endif; ?>
    <?php if (isset($_GET['mensaje'])): ?>
        <div class="mt-4 p-4 rounded <?php echo (($_GET['tipo'] ?? 'success') === 'success') ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'; ?>">
            <?php echo htmlspecialchars($_GET['mensaje']); ?>
        </div>
    <?php endif; ?>
</div>

<!-- Estadísticas rápidas -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
    <div class="bg-white p-4 rounded-lg shadow">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <i class="fas fa-inbox text-blue-500 text-xl"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-500">Bandeja</p>
                <p class="text-2xl font-semibold text-gray-900">
                    <?php echo count(obtenerTareas($pdo, 'id_proyecto IS NULL')); ?>
                </p>
            </div>
        </div>
    </div>
    
    <div class="bg-white p-4 rounded-lg shadow">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <i class="fas fa-calendar-day text-green-500 text-xl"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-500">Hoy</p>
                <p class="text-2xl font-semibold text-gray-900">
                    <?php echo count(obtenerTareas($pdo, 'DATE(fecha_vencimiento) = CURDATE()')); ?>
                </p>
            </div>
        </div>
    </div>
    
    <div class="bg-white p-4 rounded-lg shadow">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <i class="fas fa-pause-circle text-yellow-500 text-xl"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-500">En Espera</p>
                <p class="text-2xl font-semibold text-gray-900">
                    <?php echo count(obtenerTareas($pdo, "estado = 'en_espera'")); ?>
                </p>
            </div>
        </div>
    </div>
    
    <div class="bg-white p-4 rounded-lg shadow">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <i class="fas fa-folder text-purple-500 text-xl"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-500">Proyectos</p>
                <p class="text-2xl font-semibold text-gray-900">
                    <?php echo count($proyectos); ?>
                </p>
            </div>
        </div>
    </div>
</div>

<!-- Contenido específico de la vista -->
<?php
switch ($vista) {
    case 'proyectos':
        if (!isset($_GET['proyecto_id'])) {
            // Mostrar lista de proyectos
            ?>
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Proyectos</h3>
                </div>
                <div class="p-6">
                    <!-- Gestión de Proyectos -->
                    <div class="bg-gray-50 border border-gray-200 rounded-lg mb-6">
                        <div class="px-4 py-3 border-b flex items-center justify-between">
                            <h4 class="font-medium text-gray-900">Gestionar Proyectos</h4>
                            <button onclick="toggleCrearProyecto()" class="text-sm bg-blue-600 text-white px-3 py-1 rounded hover:bg-blue-700">
                                <i class="fas fa-plus mr-1"></i>Nuevo Proyecto
                            </button>
                        </div>
                        <div id="formCrearProyecto" class="hidden p-4">
                            <form action="procesar.php" method="POST" class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
                                <input type="hidden" name="accion" value="crear_proyecto">
                                <div class="md:col-span-1">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Nombre</label>
                                    <input type="text" name="nombre" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                                </div>
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Descripción</label>
                                    <input type="text" name="descripcion" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                </div>
                                <div class="md:col-span-3 flex justify-end">
                                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">Crear Proyecto</button>
                                </div>
                            </form>
                        </div>
                        <div class="p-4">
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-100">
                                        <tr>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nombre</th>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Descripción</th>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tareas</th>
                                            <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        <?php foreach ($proyectos as $proyecto): 
                                            $tareasCount = count(obtenerTareas($pdo, 'id_proyecto = ?', [$proyecto['id']]));
                                        ?>
                                        <tr>
                                            <td class="px-4 py-2">
                                                <a class="font-medium text-gray-900 hover:text-blue-600" href="index.php?vista=proyectos&proyecto_id=<?php echo $proyecto['id']; ?>">
                                                    <?php echo htmlspecialchars($proyecto['nombre']); ?>
                                                </a>
                                            </td>
                                            <td class="px-4 py-2 text-gray-600"><?php echo htmlspecialchars($proyecto['descripcion']); ?></td>
                                            <td class="px-4 py-2 text-gray-600"><?php echo $tareasCount; ?></td>
                                            <td class="px-4 py-2 text-right">
                                                <button onclick="abrirModalEditarProyecto(<?php echo $proyecto['id']; ?>, '<?php echo addslashes($proyecto['nombre']); ?>', '<?php echo addslashes($proyecto['descripcion']); ?>')" class="text-blue-600 hover:text-blue-800 text-sm mr-2">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <form action="procesar.php" method="POST" class="inline" onsubmit="return confirm('¿Eliminar este proyecto? Las tareas pasarán a la Bandeja de Entrada.');">
                                                    <input type="hidden" name="accion" value="eliminar_proyecto">
                                                    <input type="hidden" name="id" value="<?php echo $proyecto['id']; ?>">
                                                    <button type="submit" class="text-red-600 hover:text-red-800 text-sm">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        <?php foreach ($proyectos as $proyecto): 
                            $tareasCount = count(obtenerTareas($pdo, 'id_proyecto = ?', [$proyecto['id']]));
                        ?>
                            <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                                <h4 class="font-medium text-gray-900 mb-2">
                                    <a href="index.php?vista=proyectos&proyecto_id=<?php echo $proyecto['id']; ?>" 
                                       class="hover:text-blue-600">
                                        <?php echo htmlspecialchars($proyecto['nombre']); ?>
                                    </a>
                                </h4>
                                <p class="text-sm text-gray-600 mb-3"><?php echo htmlspecialchars($proyecto['descripcion']); ?></p>
                                <div class="flex items-center text-sm text-gray-500">
                                    <i class="fas fa-tasks mr-1"></i>
                                    <?php echo $tareasCount; ?> tareas
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <?php
        } else {
            // Mostrar tareas de un proyecto específico
            mostrarListaTareas($tareas, $proyectos, $contextos);
        }
        break;
        
    case 'contextos':
        if (!isset($_GET['contexto'])) {
            // Mostrar lista de contextos
            ?>
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Contextos</h3>
                </div>
                <div class="p-6">
                    <div class="space-y-2">
                        <?php foreach ($contextos as $contexto): 
                            $tareasCount = count(obtenerTareas($pdo, 'contexto = ?', [$contexto]));
                        ?>
                            <div class="flex items-center justify-between p-3 border border-gray-200 rounded-md hover:bg-gray-50">
                                <a href="index.php?vista=contextos&contexto=<?php echo urlencode($contexto); ?>" 
                                   class="font-medium text-gray-900 hover:text-blue-600">
                                    <?php echo htmlspecialchars($contexto); ?>
                                </a>
                                <span class="text-sm text-gray-500"><?php echo $tareasCount; ?> tareas</span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <?php
        } else {
            // Mostrar tareas de un contexto específico
            mostrarListaTareas($tareas, $proyectos, $contextos);
        }
        break;
        
    default:
        // Bandeja de entrada, Hoy, En Espera
        mostrarListaTareas($tareas, $proyectos, $contextos);
        break;
}

// Función auxiliar para mostrar la lista de tareas
function mostrarListaTareas($tareas, $proyectos, $contextos) {
    ?>
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
            <h3 class="text-lg font-medium text-gray-900">Tareas (<?php echo count($tareas); ?>)</h3>
            <div class="flex gap-2">
                <button onclick="mostrarFormularioTarea()" class="bg-blue-600 text-white px-3 py-1 rounded text-sm hover:bg-blue-700">
                    <i class="fas fa-plus mr-1"></i>Nueva Tarea
                </button>
            </div>
        </div>
        
        <!-- Formulario de nueva tarea (oculto por defecto) -->
        <div id="formularioNuevaTarea" class="hidden p-6 border-b border-gray-200 bg-gray-50">
            <form action="procesar.php" method="POST" class="space-y-4">
                <input type="hidden" name="accion" value="crear_tarea_completa">
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Título</label>
                        <input type="text" name="titulo" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Proyecto</label>
                        <select name="id_proyecto" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">Sin proyecto</option>
                            <?php foreach ($proyectos as $proyecto): ?>
                                <option value="<?php echo $proyecto['id']; ?>"><?php echo htmlspecialchars($proyecto['nombre']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Fecha de Vencimiento</label>
                        <input type="date" name="fecha_vencimiento" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Contexto</label>
                        <input type="text" name="contexto" placeholder="ej: @email, @trabajo" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Descripción</label>
                    <textarea name="descripcion" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Detalles, notas, enlaces..."></textarea>
                </div>
                
                <div class="flex justify-end gap-2">
                    <button type="button" onclick="ocultarFormularioTarea()" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                        Cancelar
                    </button>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                        Crear Tarea
                    </button>
                </div>
            </form>
        </div>
        
        <div class="p-6">
            <?php if (empty($tareas)): ?>
                <div class="text-center py-8">
                    <i class="fas fa-tasks text-gray-300 text-4xl mb-4"></i>
                    <p class="text-gray-500">No hay tareas para mostrar</p>
                </div>
            <?php else: ?>
                <div class="space-y-3">
                    <?php foreach ($tareas as $tarea): ?>
                        <div class="flex items-center justify-between p-4 border border-gray-200 rounded-lg hover:bg-gray-50 <?php echo $tarea['estado'] == 'completada' ? 'bg-green-50 border-green-200' : ''; ?>">
                            <div class="flex items-center flex-1">
                                <form action="procesar.php" method="POST" class="mr-3">
                                    <input type="hidden" name="accion" value="cambiar_estado">
                                    <input type="hidden" name="id" value="<?php echo $tarea['id']; ?>">
                                    <input type="hidden" name="estado_actual" value="<?php echo $tarea['estado']; ?>">
                                    <button type="submit" class="text-gray-400 hover:text-gray-600">
                                        <?php if ($tarea['estado'] == 'completada'): ?>
                                            <i class="fas fa-check-circle text-green-500 text-xl"></i>
                                        <?php else: ?>
                                            <i class="far fa-circle text-xl"></i>
                                        <?php endif; ?>
                                    </button>
                                </form>
                                
                                <div class="flex-1">
                                    <h4 class="font-medium text-gray-900 <?php echo $tarea['estado'] == 'completada' ? 'line-through text-gray-500' : ''; ?>">
                                        <?php echo htmlspecialchars($tarea['titulo']); ?>
                                    </h4>
                                    <div class="flex items-center text-sm text-gray-500 mt-1">
                                        <?php if ($tarea['proyecto_nombre']): ?>
                                            <span class="mr-3">
                                                <i class="fas fa-folder mr-1"></i><?php echo htmlspecialchars($tarea['proyecto_nombre']); ?>
                                            </span>
                                        <?php endif; ?>
                                        <?php if ($tarea['fecha_vencimiento']): ?>
                                            <span class="mr-3">
                                                <i class="fas fa-calendar mr-1"></i><?php echo date('d/m/Y', strtotime($tarea['fecha_vencimiento'])); ?>
                                            </span>
                                        <?php endif; ?>
                                        <?php if ($tarea['contexto']): ?>
                                            <span class="mr-3">
                                                <i class="fas fa-tag mr-1"></i><?php echo htmlspecialchars($tarea['contexto']); ?>
                                            </span>
                                        <?php endif; ?>
                                        <?php if ($tarea['estado'] == 'en_espera'): ?>
                                            <span class="bg-yellow-100 text-yellow-800 px-2 py-1 rounded-full text-xs">
                                                <i class="fas fa-pause mr-1"></i>En Espera
                                            </span>
                                        <?php endif; ?>
                                        <?php
                                        // Mostrar badge de vencida si aplica
                                        if (!empty($tarea['fecha_vencimiento']) && strtotime($tarea['fecha_vencimiento']) < strtotime(date('Y-m-d')) && $tarea['estado'] != 'completada'): ?>
                                            <span class="bg-red-100 text-red-800 px-2 py-1 rounded-full text-xs">
                                                <i class="fas fa-triangle-exclamation mr-1"></i>Vencida
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                    <?php if (!empty($tarea['descripcion'])): ?>
                                        <p class="text-sm text-gray-700 mt-2"><?php echo nl2br(htmlspecialchars($tarea['descripcion'])); ?></p>
                                    <?php endif; ?>
                                </div>
                            </div>
                            
                            <div class="flex items-center gap-2">
                                <button onclick="abrirModalEditarTarea(<?php echo $tarea['id']; ?>, '<?php echo addslashes($tarea['titulo']); ?>', '<?php echo $tarea['id_proyecto']; ?>', '<?php echo $tarea['fecha_vencimiento']; ?>', '<?php echo addslashes($tarea['contexto']); ?>', '<?php echo addslashes($tarea['descripcion'] ?? ''); ?>', '<?php echo $tarea['estado']; ?>')" 
                                        class="text-blue-600 hover:text-blue-800 text-sm">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <form action="procesar.php" method="POST" class="inline">
                                    <input type="hidden" name="accion" value="eliminar_tarea">
                                    <input type="hidden" name="id" value="<?php echo $tarea['id']; ?>">
                                    <button type="submit" class="text-red-600 hover:text-red-800 text-sm" onclick="return confirm('¿Estás seguro de eliminar esta tarea?')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
    <?php
}
?>

<script>
function mostrarFormularioTarea() {
    document.getElementById('formularioNuevaTarea').classList.remove('hidden');
}

function ocultarFormularioTarea() {
    document.getElementById('formularioNuevaTarea').classList.add('hidden');
}
</script>
<script>
function toggleCrearProyecto() {
    const el = document.getElementById('formCrearProyecto');
    if (!el) return;
    el.classList.toggle('hidden');
}
</script>

<?php require_once 'includes/footer.php'; ?>