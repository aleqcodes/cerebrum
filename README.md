# GTD Productividad — Get the Shit Done

Un gestor de tareas minimalista, rápido y en español, inspirado en Getting Things Done (GTD). Construido con PHP + MySQL, Tailwind CSS y Font Awesome.

## ✨ Características

- Captura rápida en el encabezado para añadir tareas al instante.
- Vistas clave: Bandeja de Entrada, Hoy, Vencidas, Proyectos, Contextos y En Espera.
- Descripción de tareas: creación y edición con campo de texto enriquecido (simple).
- Modal de edición: título, estado, proyecto, contexto, fecha y descripción.
- Indicador visual de tareas vencidas.
- Sidebar adaptable y encabezado móvil horizontal con el lema “Get the shit Done”.
- Autenticación básica: registro del primer usuario, login y logout con contraseñas cifradas.

## 🧱 Stack

- PHP 8.x (servidor embebido o MAMP/WAMP/XAMPP)
- MySQL 5.7+ (o MariaDB) con `pdo_mysql`
- Tailwind CSS (CDN)
- Font Awesome (CDN)

## 🚀 Puesta en marcha

1. Clona el repositorio:
   ```bash
   git clone https://github.com/aleqcodes/cerebrum.git
   cd cerebrum
   ```
2. Crea la base de datos e importa el esquema (y datos de ejemplo):
   - Abre tu cliente MySQL y ejecuta el contenido de `database.sql`.
3. Configura la conexión en `db.php`:
   - Ajusta `$host`, `$dbname`, `$username` y `$password` a tu entorno.
4. Arranca el servidor de desarrollo de PHP (opción genérica):
   ```bash
   php -S 127.0.0.1:8082 -t .
   ```
   - En Windows con MAMP, también puedes usar:
   ```powershell
   "C:\MAMP\bin\php\php8.3.1\php.exe" -S 127.0.0.1:8082 -t .
   ```
5. Abre el navegador y registra el primer usuario:
   - `http://127.0.0.1:8082/register.php`
6. Inicia sesión:
   - `http://127.0.0.1:8082/login.php`
7. Comienza a usar la app desde `index.php`.

## 🧭 Uso rápido

- Captura rápida: escribe en el campo superior y pulsa “Añadir”.
- Crear/editar tareas: usa el formulario completo y el modal de edición.
- Cambiar estado: botón de check alterna entre “pendiente” y “completada”.
- Eliminar: icono de papelera en la tarjeta de la tarea.
- Navegación:
  - Hoy: tareas con vencimiento hoy.
  - Vencidas: tareas con fecha en el pasado y no completadas.
  - Proyectos/Contextos: vistas filtradas por proyecto o etiqueta/contexto.

## 🔐 Seguridad

- Modo mono-usuario: el sitio completo se protege con login.
- Contraseñas seguras con `password_hash` y verificación con `password_verify`.
- Para multi-usuario (cada persona ve solo sus datos), se sugiere:
  - Añadir `usuario_id` a `tareas` y `proyectos`.
  - Filtrar consultas por el usuario autenticado.
  - Migrar datos existentes y ajustar formularios.

## 📁 Estructura

```
├── auth.php
├── database.sql
├── db.php
├── includes/
│   ├── header.php
│   └── footer.php
├── index.php
├── login.php
├── procesar.php
└── register.php
```

## ⚙️ Configuración

- `db.php` inicia la sesión (`session_start`) y define la conexión PDO.
- `auth.php` gestiona `require_login`, existencia de usuarios y usuario actual.
- `procesar.php` centraliza acciones: crear/editar/eliminar tareas y proyectos, login, logout y registro.

## 🗺️ Roadmap sugerido

- Multi-usuario con propiedad de tareas y proyectos.
- Búsqueda y filtros avanzados.
- Notificaciones y recordatorios.
- Captura rápida extendida con descripción.
- Header móvil sticky y tema oscuro.
- Pruebas automatizadas y CI.

## 🤝 Contribuir

1. Haz un fork y crea una rama: `feature/tu-mejora`.
2. Asegúrate de que el servidor arranca y que las vistas funcionan.
3. Envía un Pull Request explicando claramente tu cambio.

## 📜 Licencia

Este repositorio es público en GitHub. Te recomendamos añadir una licencia (por ejemplo, MIT o Apache 2.0) para clarificar usos y contribuciones.

---

Hecho con foco y cariño para que completes tus tareas. ¡Get the shit Done! 💪