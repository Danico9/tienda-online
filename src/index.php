<?php
/**
 * Página principal - Listado de productos
 *
 * Muestra todos los productos del catálogo con funcionalidad de búsqueda.
 * Incluye tabla con información detallada y botones de acción según rol.
 * Disponible para todos los usuarios (admin y regulares).
 */

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/header.php';
require_once __DIR__ . '/services/SessionService.php';
require_once __DIR__ . '/config/Config.php';
require_once __DIR__ . '/services/ProductosService.php';
require_once __DIR__ . '/services/CategoriasService.php';

use services\SessionService;
use config\Config;
use services\ProductosService;
use services\CategoriasService;

// Inicializa servicios necesarios
$session = SessionService::getInstance();
$config = Config::getInstance();
$productosService = new ProductosService($config->db);
$categoriasService = new CategoriasService($config->db);

// Obtiene término de búsqueda y lista de productos
$searchTerm = $_GET['search'] ?? '';
$productos = $productosService->findAllWithCategoryName($searchTerm);
?>

    <!-- Encabezado de bienvenida -->
    <div class="row mb-4">
        <div class="col">
            <h1>Bienvenido, <?= htmlspecialchars($session->getUsername()) ?></h1>
            <p class="text-muted">Sistema de gestión de productos</p>
        </div>
    </div>

    <!-- Mensaje de éxito al eliminar -->
<?php if (isset($_GET['deleted'])): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        Producto eliminado correctamente
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

    <!-- Mensaje de error -->
<?php if (isset($_GET['error'])): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        Error al procesar la operación
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

    <!-- Formulario de búsqueda -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="index.php" class="row g-3">
                <div class="col-md-10">
                    <input type="text" name="search" class="form-control"
                           placeholder="Buscar por marca o modelo..."
                           value="<?= htmlspecialchars($searchTerm) ?>">
                </div>
                <div class="col-md-2">
                    <button class="btn btn-primary w-100" type="submit">Buscar</button>
                </div>
            </form>
            <!-- Botón para limpiar búsqueda y contador de resultados -->
            <?php if (!empty($searchTerm)): ?>
                <div class="mt-2">
                    <a href="index.php" class="btn btn-sm btn-secondary">Limpiar búsqueda</a>
                    <span class="text-muted ms-2">(<?= count($productos) ?> resultados)</span>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Tabla de productos -->
    <div class="card">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Listado de Productos (<?= count($productos) ?>)</h5>
        </div>
        <div class="card-body">
            <?php if (empty($productos)): ?>
                <!-- Mensaje cuando no hay productos -->
                <div class="alert alert-info">
                    No se encontraron productos
                    <?php if (!empty($searchTerm)): ?>
                        que coincidan con "<?= htmlspecialchars($searchTerm) ?>"
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Imagen</th>
                            <th>Marca</th>
                            <th>Modelo</th>
                            <th>Descripción</th>
                            <th>Precio</th>
                            <th>Stock</th>
                            <th>Categoría</th>
                            <th>Acciones</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($productos as $producto): ?>
                            <tr>
                                <td><?= $producto->id ?></td>
                                <td>
                                    <img src="<?= htmlspecialchars($producto->imagen) ?>"
                                         width="50" height="50"
                                         alt="Producto"
                                         class="img-thumbnail">
                                </td>
                                <td><?= htmlspecialchars($producto->marca) ?></td>
                                <td><?= htmlspecialchars($producto->modelo) ?></td>
                                <td><?= htmlspecialchars(substr($producto->descripcion, 0, 50)) ?>...</td>
                                <td><strong>$<?= number_format($producto->precio, 2) ?></strong></td>
                                <td>
                                    <!-- Badge de stock con colores según disponibilidad -->
                                    <?php if ($producto->stock > 0): ?>
                                        <span class="badge bg-success"><?= $producto->stock ?> unidades</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger">Sin stock</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="badge bg-info">
                                        <?= htmlspecialchars($producto->categoriaNombre ?? 'Sin categoría') ?>
                                    </span>
                                </td>
                                <td>
                                    <!-- Botones de acción según permisos -->
                                    <div class="d-flex flex-wrap gap-2">
                                        <a href="details.php?id=<?= $producto->id ?>"
                                           class="btn btn-sm btn-info"
                                           title="Ver detalles">
                                            Detalles
                                        </a>
                                        <!-- Botones exclusivos para administradores -->
                                        <?php if ($session->isAdmin()): ?>
                                            <a href="update.php?id=<?= $producto->id ?>"
                                               class="btn btn-sm btn-warning"
                                               title="Editar">
                                                Editar
                                            </a>
                                            <a href="update-image.php?id=<?= $producto->id ?>"
                                               class="btn btn-sm btn-secondary"
                                               title="Cambiar imagen">
                                                Imagen
                                            </a>
                                            <a href="delete.php?id=<?= $producto->id ?>"
                                               class="btn btn-sm btn-danger"
                                               title="Eliminar"
                                               onclick="return confirm('¿Está seguro de eliminar este producto?')">
                                                Eliminar
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Botón crear producto solo para administradores -->
<?php if ($session->isAdmin()): ?>
    <div class="mt-4">
        <a href="create.php" class="btn btn-success btn-lg">
            Crear Nuevo Producto
        </a>
    </div>
<?php endif; ?>

    <!-- Información de sesión para usuarios autenticados -->
<?php if ($session->isLoggedIn()): ?>
    <div class="card mt-4">
        <div class="card-body">
            <h5 class="card-title">Información de Sesión</h5>
            <ul class="list-group list-group-flush">
                <li class="list-group-item">
                    <strong>Visitas:</strong> <?= $session->getVisits() ?>
                </li>
                <li class="list-group-item">
                    <strong>Último login:</strong>
                    <?= $session->getLoginTime() ?? 'No disponible' ?>
                </li>
            </ul>
        </div>
    </div>
<?php endif; ?>

<?php require_once __DIR__ . '/footer.php'; ?>