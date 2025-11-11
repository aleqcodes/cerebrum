</div>
        </div>
    </div>

    <!-- Modal para editar tarea -->
    <div id="editarTareaModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Editar Tarea</h3>
                <form id="formEditarTarea" action="procesar.php" method="POST">
                    <input type="hidden" name="accion" value="actualizar_tarea">
                    <input type="hidden" name="id" id="editar_id">
                    
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Título</label>
                        <input type="text" name="titulo" id="editar_titulo" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                    </div>
                    
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Proyecto</label>
                        <select name="id_proyecto" id="editar_id_proyecto" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">Sin proyecto</option>
                            <?php
                            $proyectos = obtenerProyectos($pdo);
                            foreach ($proyectos as $proyecto) {
                                echo '<option value="' . $proyecto['id'] . '">' . htmlspecialchars($proyecto['nombre']) . '</option>';
                            }
                            ?>
                        </select>
                    </div>
                    
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Fecha de Vencimiento</label>
                        <input type="date" name="fecha_vencimiento" id="editar_fecha_vencimiento" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Contexto</label>
                        <input type="text" name="contexto" id="editar_contexto" placeholder="ej: @email, @trabajo" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Descripción</label>
                        <textarea name="descripcion" id="editar_descripcion" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Detalles, notas, enlaces..."></textarea>
                    </div>
                    
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Estado</label>
                        <select name="estado" id="editar_estado" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="pendiente">Pendiente</option>
                            <option value="en_espera">En Espera</option>
                            <option value="completada">Completada</option>
                        </select>
                    </div>
                    
                    <div class="flex justify-end gap-3">
                        <button type="button" onclick="cerrarModal('editarTareaModal')" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500">
                            Cancelar
                        </button>
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            Guardar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal para editar proyecto -->
    <div id="editarProyectoModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Editar Proyecto</h3>
                <form id="formEditarProyecto" action="procesar.php" method="POST">
                    <input type="hidden" name="accion" value="actualizar_proyecto">
                    <input type="hidden" name="id" id="editar_proyecto_id">

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Nombre</label>
                        <input type="text" name="nombre" id="editar_proyecto_nombre" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Descripción</label>
                        <input type="text" name="descripcion" id="editar_proyecto_descripcion" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>

                    <div class="flex justify-end gap-3">
                        <button type="button" onclick="cerrarModal('editarProyectoModal')" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500">
                            Cancelar
                        </button>
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            Guardar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
    function abrirModalEditarTarea(id, titulo, idProyecto, fechaVencimiento, contexto, descripcion, estado) {
        document.getElementById('editar_id').value = id;
        document.getElementById('editar_titulo').value = titulo;
        document.getElementById('editar_id_proyecto').value = idProyecto || '';
        document.getElementById('editar_fecha_vencimiento').value = fechaVencimiento || '';
        document.getElementById('editar_contexto').value = contexto || '';
        document.getElementById('editar_descripcion').value = descripcion || '';
        document.getElementById('editar_estado').value = estado;
        document.getElementById('editarTareaModal').classList.remove('hidden');
    }
    
    function abrirModalEditarProyecto(id, nombre, descripcion) {
        document.getElementById('editar_proyecto_id').value = id;
        document.getElementById('editar_proyecto_nombre').value = nombre || '';
        document.getElementById('editar_proyecto_descripcion').value = descripcion || '';
        document.getElementById('editarProyectoModal').classList.remove('hidden');
    }
    
    function cerrarModal(modalId) {
        document.getElementById(modalId).classList.add('hidden');
    }

    // Toggle del menú lateral en móvil (off-canvas)
    function toggleSidebar() {
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('sidebarOverlay');
        if (!sidebar || !overlay) return;
        const cerrado = sidebar.classList.contains('-translate-x-full');
        if (cerrado) {
            sidebar.classList.remove('-translate-x-full');
            sidebar.classList.add('translate-x-0');
            overlay.classList.remove('hidden');
        } else {
            sidebar.classList.add('-translate-x-full');
            sidebar.classList.remove('translate-x-0');
            overlay.classList.add('hidden');
        }
    }
    
    // Cerrar modal al hacer clic fuera de él
    window.onclick = function(event) {
        const modalTarea = document.getElementById('editarTareaModal');
        const modalProyecto = document.getElementById('editarProyectoModal');
        if (event.target === modalTarea) {
            modalTarea.classList.add('hidden');
        }
        if (event.target === modalProyecto) {
            modalProyecto.classList.add('hidden');
        }
    }
    </script>
</body>
</html>