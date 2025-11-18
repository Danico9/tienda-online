# Tienda Online - Sistema CRUD de Productos

Sistema de gestión de productos desarrollado en PHP puro para asignatura Desarrollo Web en Entorno Servidor del ciclo de 2º DAW Semipresencial en el IES Juan de Garay.

## Descripción del Proyecto

Aplicación web que permite gestionar un catálogo de productos mediante operaciones CRUD (Crear, Leer, Actualizar, Eliminar). Incluye sistema de autenticación con roles diferenciados (ADMIN/USER), gestión de categorías, búsqueda de productos y subida de imágenes.

El proyecto implementa una arquitectura por capas con separación de responsabilidades, utilizando el patrón Singleton para la configuración y gestión de sesiones, y PDO con prepared statements para garantizar la seguridad en el acceso a datos.

## Tecnologías Utilizadas

- **PHP 8.2.12** - Lenguaje de programación backend
- **MySQL 8.0** - Sistema de gestión de base de datos
- **Apache 2.4.58** - Servidor web (incluido en XAMPP)
- **Bootstrap 5.1.3** - Framework CSS para diseño responsive
- **Composer 2.9.1** - Gestor de dependencias
- **PDO** - Capa de abstracción para acceso a base de datos

### Dependencias (instaladas vía Composer)

- `vlucas/phpdotenv` (^5.6) - Gestión de variables de entorno
- `ramsey/uuid` (^4.7) - Generación de identificadores únicos

## Estructura del Proyecto
```
tienda-online/
│
├── database/
│   └── init.sql                    # Script de creación de base de datos
│
├── src/
│   ├── config/
│   │   └── Config.php             # Configuración y conexión a BD
│   │
│   ├── models/
│   │   ├── Categoria.php          # Modelo de datos: Categoría
│   │   ├── Producto.php           # Modelo de datos: Producto
│   │   └── User.php               # Modelo de datos: Usuario
│   │
│   ├── services/
│   │   ├── CategoriasService.php  # Lógica de negocio: categorías
│   │   ├── ProductosService.php   # Lógica de negocio: productos
│   │   ├── SessionService.php     # Gestión de sesiones
│   │   └── UsersService.php       # Autenticación de usuarios
│   │
│   ├── uploads/                   # Carpeta para imágenes de productos
│   │
│   ├── create.php                 # Formulario de creación
│   ├── delete.php                 # Eliminación de productos
│   ├── details.php                # Vista de detalles
│   ├── footer.php                 # Pie de página común
│   ├── header.php                 # Cabecera y navegación
│   ├── index.php                  # Página principal (listado)
│   ├── login.php                  # Inicio de sesión
│   ├── logout.php                 # Cierre de sesión
│   ├── update.php                 # Formulario de edición
│   ├── update-image.php           # Cambio de imagen
│   └── update_image_file.php      # Procesamiento de imagen
│
├── vendor/                        # Dependencias (generado por Composer)
├── .env                           # Variables de entorno
├── .gitignore                     # Archivos excluidos de Git
├── composer.json                  # Definición de dependencias
└── README.md                      # Este archivo
```

### Descripción de carpetas

- **database/**: Contiene el script SQL para crear e inicializar la base de datos.
- **src/config/**: Configuración de la aplicación y conexión a la base de datos.
- **src/models/**: Clases que representan las entidades del sistema.
- **src/services/**: Lógica de negocio y operaciones sobre la base de datos.
- **src/uploads/**: Almacenamiento de imágenes subidas de productos.
- **vendor/**: Librerías externas instaladas por Composer (no incluir en Git).

## Requisitos Previos

Antes de instalar el proyecto, asegúrate de tener:

### 1. XAMPP (versión 8.0 o superior)

XAMPP es un paquete que incluye Apache, MySQL, PHP y otras herramientas necesarias para ejecutar aplicaciones web en local.

**Descargar e instalar:**

1. Ve a la página oficial: https://www.apachefriends.org/
2. Descarga la versión para tu sistema operativo (Windows, Linux o macOS)
3. Ejecuta el instalador
4. Durante la instalación, asegúrate de seleccionar:
    - Apache
    - MySQL
    - PHP
    - phpMyAdmin
5. Instala en la ruta por defecto: `C:/xampp` (Windows) o `/opt/lampp` (Linux)
6. Finaliza la instalación

**Verificar la instalación:**

1. Abre XAMPP Control Panel
2. Inicia los módulos Apache y MySQL
3. Abre tu navegador y ve a: http://localhost
4. Deberías ver la página de bienvenida de XAMPP
5. Verifica phpMyAdmin en: http://localhost/phpmyadmin

**Configuración adicional (opcional):**

Si MySQL no arranca por conflicto de puertos, cambia el puerto por defecto:
1. En XAMPP Control Panel, haz clic en "Config" junto a MySQL
2. Selecciona "my.ini"
3. Busca la línea `port=3306` y cámbiala a `port=3307`
4. Guarda y reinicia MySQL

### 2. Composer (gestor de dependencias de PHP)

Composer es una herramienta que permite gestionar las librerías y dependencias de proyectos PHP.

**Descargar e instalar en Windows:**

1. Ve a: https://getcomposer.org/download/
2. Descarga "Composer-Setup.exe"
3. Ejecuta el instalador
4. El instalador detectará automáticamente la ubicación de PHP (desde XAMPP)
    - Si no la detecta, indica manualmente: `C:/xampp/php/php.exe`
5. Completa la instalación dejando las opciones por defecto
6. Reinicia tu terminal o símbolo del sistema

**Descargar e instalar en Linux/macOS:**
```bash
# Descargar el instalador
php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"

# Verificar el instalador (opcional)
php -r "if (hash_file('sha384', 'composer-setup.php') === 'dac665fdc30fdd8ec78b38b9800061b4150413ff2e3b6f88543c636f7cd84f6db9189d43a81e5503cda447da73c7e5b6') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;"

# Instalar Composer globalmente
php composer-setup.php --install-dir=/usr/local/bin --filename=composer

# Eliminar el instalador
php -r "unlink('composer-setup.php');"
```

**Verificar la instalación:**

Abre una terminal (CMD, PowerShell, o terminal de Linux/macOS) y ejecuta:
```bash
composer --version
```

Deberías ver algo como: `Composer version 2.x.x`

**Solución de problemas:**

Si el comando `composer` no es reconocido en Windows:
1. Cierra y vuelve a abrir la terminal
2. Si persiste, verifica que la ruta de Composer esté en las variables de entorno PATH
3. Reinicia el ordenador si es necesario

**Ejecutar Composer Install**

Una vez instalado Composer, se debe instalar las dependencias del proyecto.
1. Abre una terminal en la carpeta raíz del proyecto y ejecuta:
```bash
composer install
```

2. Descargará automáticamente todas las librerías definidas en composer.json
3. Creará la carpeta vendor/:
4. Generará el archivo vendor/autoload.php, necesario para cargar las dependencias

Si más adelante se añaden nuevas dependencias o descargas el proyecto de nuevo, simplemente vuelve a ejecutar:
composer install


## Instalación

### 1. Clonar o descargar el repositorio (IMPORTANTE, TIENE QUE SER DENTRO DE LA CARPETA XAMPP)
```bash
git clone https://github.com/Danico9/tienda-online
cd tienda-online
```

O descarga el ZIP y extráelo en `C:/xampp/htdocs/tienda-online`

### 2. Instalar dependencias

Abre una terminal en la carpeta del proyecto y ejecuta:
```bash
composer install
```

Este comando instalará las librerías necesarias (`phpdotenv` y `ramsey/uuid`).

### 3. Configurar variables de entorno

El archivo `.env` ya está incluido. Verifica que las credenciales coincidan con tu instalación de XAMPP:
```env
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=tienda
DB_USERNAME=root
DB_PASSWORD=
```

Si tu instalación de XAMPP tiene contraseña en MySQL, cámbiala en `DB_PASSWORD`.

Ajusta también las rutas de subida de imágenes si es necesario:
```env
UPLOAD_PATH=C:/xampp/htdocs/tienda-online/src/uploads/
UPLOAD_URL=http://localhost/tienda-online/src/uploads/
```

### 4. Crear la base de datos

1. Inicia XAMPP Control Panel y arranca los servicios Apache y MySQL
2. Abre phpMyAdmin en: http://localhost/phpmyadmin
3. Haz clic en "Importar"
4. Selecciona el archivo `database/init.sql`
5. Haz clic en "Continuar"

El script creará automáticamente:
- La base de datos `tienda`
- Las tablas necesarias (productos, categorias, usuarios, user_roles)
- Datos de prueba (5 productos, 5 categorías, 3 usuarios)

### 5. Verificar carpeta de uploads

Asegúrate de que existe la carpeta `src/uploads/` con permisos de escritura. Si no existe, créala manualmente.

### 6. Acceder a la aplicación

Abre tu navegador y accede a:
```
http://localhost/tienda-online/src/index.php
```

Deberías ver la página principal con el listado de productos.

## Uso Básico

### Inicio de sesión

Para acceder a las funcionalidades de administración, haz clic en "Iniciar Sesión" en la barra de navegación.

### Usuarios de prueba

| Usuario | Contraseña | Rol | Permisos |
|---------|------------|-----|----------|
| admin | Admin1 | ADMIN | Acceso completo (crear, editar, eliminar) |
| user | User1234 | USER | Solo lectura (ver listado y detalles) |
| test | test1234 | USER | Solo lectura |

Si quisiera crear un usuario nuevo:

```
-- Generar hash contraseña
php -r "echo password_hash('prueba', PASSWORD_BCRYPT);"

-- Crear usuario prueba con contraseña prueba
INSERT INTO usuarios (username, password, nombre, apellidos, email)
VALUES ('prueba', '$2y$10$IJojCU9Z4wKtpnfl9S4OR.wQJ02YCqkfK1P.Nm9eAy.6tEQJJ9Naq', 'Daniel', 'Danientest', 'email@test.com');

-- Asignar roles
SET @user_id = LAST_INSERT_ID();
INSERT INTO user_roles (user_id, roles) VALUES (@user_id, 'USER');
```

### Funcionalidades según rol

**Usuario regular (USER):**
- Ver listado de productos
- Buscar productos por marca o modelo
- Ver detalles de cada producto

**Administrador (ADMIN):**
- Todas las funciones de usuario regular
- Crear nuevos productos
- Editar productos existentes
- Cambiar imágenes de productos
- Eliminar productos

### Navegación

- **Página principal**: Muestra listado completo de productos con búsqueda
- **Crear producto**: Formulario con validación (solo admin)
- **Editar producto**: Formulario pre-rellenado (solo admin)
- **Cambiar imagen**: Subida de archivos JPG/PNG con validación (solo admin)
- **Detalles**: Vista completa de información del producto

## Funcionalidades Implementadas

### Gestión de Productos (CRUD)

- **Crear**: Formulario con validación de campos obligatorios, asignación automática de UUID e imagen por defecto
- **Leer**: Listado con tabla responsive, visualización de imágenes, búsqueda por marca/modelo
- **Actualizar**: Edición de datos con validación, actualización de timestamp automática
- **Eliminar**: Confirmación previa, eliminación física de registro e imagen asociada

### Sistema de Autenticación

- Login con validación de credenciales
- Contraseñas hasheadas con bcrypt
- Control de sesiones con expiración automática (1 hora de inactividad)
- Gestión de roles (ADMIN/USER)
- Protección de rutas según permisos

### Gestión de Imágenes

- Subida de archivos JPG/PNG
- Validación de tipo MIME y tamaño (máximo 2MB)
- Nombres únicos basados en UUID del producto
- Eliminación de imagen anterior al actualizar

### Seguridad

- Prepared statements para prevenir inyección SQL
- Password hashing con bcrypt
- Validación y sanitización de inputs con `filter_input()`
- Control de acceso basado en roles
- Validación de tipos de archivo en subidas

## Patrones de Diseño Aplicados

### Singleton

Implementado en `Config.php` y `SessionService.php` para garantizar una única instancia de configuración y sesión durante la ejecución.

### Separación de Responsabilidades

- **Modelos**: Estructuras de datos sin lógica de negocio
- **Servicios**: Lógica de negocio y operaciones con base de datos
- **Vistas**: Presentación de información al usuario
- **Configuración**: Gestión centralizada de parámetros

## Dificultades Encontradas y Soluciones

### Gestión de rutas

**Problema**: Las rutas relativas fallaban al navegar entre páginas.

**Solución**: Usar rutas absolutas desde la raíz del proyecto.

### Persistencia de sesiones

**Problema**: Las sesiones no se mantenían correctamente.

**Solución**: Implementar patrón Singleton en SessionService con control de expiración.

### Permisos de escritura

**Problema**: Errores al guardar imágenes en la carpeta uploads.

**Solución**: Verificar permisos de la carpeta y crear automáticamente si no existe.

### Validación de contraseñas

**Problema**: Comparación incorrecta de contraseñas.

**Solución**: Usar `password_hash()` al guardar y `password_verify()` al autenticar.

## Posibles Mejoras Futuras

- Implementar paginación en el listado de productos
- Añadir filtros por categoría y rango de precio
- Sistema de carrito de compra
- Panel de administración más completo con estadísticas
- Exportación de productos a CSV/PDF
- Implementar tests unitarios con PHPUnit
- Migración a framework PHP (Laravel)

## Autor

**Daniel**

Estudiante de 2º DAW Semipresencial - IES Juan de Garay, Valencia

- GitHub: https://github.com/Danico9/tienda-online
- Email: dancisbla@alu.edu.gva.es

## Licencia

Este proyecto ha sido desarrollado con fines educativos.

Código disponible bajo licencia Uso educativo.

---

**Proyecto desarrollado para el curso 2025-2026**