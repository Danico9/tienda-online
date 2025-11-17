<?php
/**
 * Vista de detalles de producto
 *
 * Muestra toda la información detallada de un producto específico.
 * Incluye imagen, datos técnicos, precio, stock y categoría.
 * Botones de acción disponibles solo para administradores.
 */

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/services/SessionService.php';
require_once __DIR__ . '/services/ProductosService.php';
require_once __DIR__ . '/config/Config.php';

use services\SessionService;
use services\ProductosService;
use config\Config;

$session = SessionService::getInstance();
$config = Config::getInstance();
$productosService = new ProductosService($config->db);

// Valida que se reciba un ID válido
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id) {
    header('Location: /tienda-online/src/index.php');
    exit;
}

// Busca el producto por ID
$producto = $productosService->findById($id);
if (!$producto) {
    header('Location: /tienda-online/src/index.php');
    exit;
}

require_once __DIR__ . '/header.php';
?>

    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card shadow">
                <div class="card-header bg-info text-white">
                    <h2 class="mb-0">Detalles del Producto</h2>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Columna de imagen -->
                        <div class="col-md-4 text-center">
                            <img src="<?= htmlspecialchars($producto->imagen) ?>"
                                 class="img-fluid rounded shadow"
                                 alt="Producto">
                        </div>

                        <!-- Columna de datos -->
                        <div class="col-md-8">
                            <table class="table table-bordered">
                                <tbody>
                                <tr>
                                    <th width="30%">ID:</th>
                                    <td><?= $producto->id ?></td>
                                </tr>
                                <tr>
                                    <th>UUID:</th>
                                    <td><code><?= htmlspecialchars($producto->uuid) ?></code></td>
                                </tr>
                                <tr>
                                    <th>Marca:</th>
                                    <td><strong><?= htmlspecialchars($producto->marca) ?></strong></td>
                                </tr>
                                <tr>
                                    <th>Modelo:</th>
                                    <td><strong><?= htmlspecialchars($producto->modelo) ?></strong></td>
                                </tr>
                                <tr>
                                    <th>Descripción:</th>
                                    <td><?= htmlspecialchars($producto->descripcion) ?></td>
                                </tr>
                                <tr>
                                    <th>Precio:</th>
                                    <td><span class="badge bg-success fs-5">$<?= number_format($producto->precio, 2) ?></span></td>
                                </tr>
                                <tr>
                                    <th>Stock:</th>
                                    <td>
                                        <?php if ($producto->stock > 0): ?>
                                            <span class="badge bg-success"><?= $producto->stock ?> unidades</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger">Sin stock</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Categoría:</th>
                                    <td><span class="badge bg-info"><?= htmlspecialchars($producto->categoriaNombre ?? 'Sin categoría') ?></span></td>
                                </tr>
                                <tr>
                                    <th>Creado:</th>
                                    <td><?= $producto->createdAt ?></td>
                                </tr>
                                <tr>
                                    <th>Actualizado:</th>
                                    <td><?= $producto->updatedAt ?></td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Pie con botones de acción -->
                <div class="card-footer">
                    <div class="d-flex gap-2">
                        <a href="/tienda-online/src/index.php" class="btn btn-secondary">
                            Volver al listado
                        </a>

                        <!-- Botones exclusivos para administradores -->
                        <?php if ($session->isAdmin()): ?>
                            <a href="update.php?id=<?= $producto->id ?>" class="btn btn-warning">
                                Editar
                            </a>
                            <a href="update-image.php?id=<?= $producto->id ?>" class="btn btn-secondary">
                                Cambiar Imagen
                            </a>
                            <a href="delete.php?id=<?= $producto->id ?>"
                               class="btn btn-danger"
                               onclick="return confirm('¿Está seguro de eliminar este producto?')">
                                Eliminar
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

<?php require_once __DIR__ . '/footer.php'; ?>