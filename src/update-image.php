<?php
/**
 * Formulario de actualización de imagen de producto
 *
 * Permite a los administradores cambiar la imagen de un producto.
 * Muestra la imagen actual y un formulario para subir nueva imagen.
 * Acceso restringido: solo administradores.
 */

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/services/SessionService.php';
require_once __DIR__ . '/services/ProductosService.php';
require_once __DIR__ . '/config/Config.php';

use services\SessionService;
use services\ProductosService;
use config\Config;

$session = SessionService::getInstance();

// Control de acceso: solo administradores pueden actualizar imágenes
if (!$session->isAdmin()) {
    header('Location: /tienda-online/src/index.php');
    exit;
}

// Valida que se reciba un ID válido
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id) {
    header('Location: /tienda-online/src/index.php');
    exit;
}

// Inicializa servicios y busca el producto
$config = Config::getInstance();
$productosService = new ProductosService($config->db);

$producto = $productosService->findById($id);
if (!$producto) {
    header('Location: /tienda-online/src/index.php');
    exit;
}

// Verifica si viene de una actualización exitosa
$success = isset($_GET['success']);

require_once __DIR__ . '/header.php';
?>

    <div class="row">
        <div class="col-md-6 offset-md-3">
            <div class="card shadow">
                <div class="card-header bg-secondary text-white">
                    <h2 class="mb-0">Actualizar Imagen del Producto</h2>
                </div>
                <div class="card-body">
                    <!-- Mensaje de éxito -->
                    <?php if ($success): ?>
                        <div class="alert alert-success alert-dismissible fade show">
                            Imagen actualizada correctamente
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <!-- Información del producto -->
                    <div class="card mb-3">
                        <div class="card-body">
                            <h5 class="card-title">Información del Producto</h5>
                            <table class="table table-sm">
                                <tr>
                                    <th width="30%">ID:</th>
                                    <td><?= $producto->id ?></td>
                                </tr>
                                <tr>
                                    <th>Marca:</th>
                                    <td><?= htmlspecialchars($producto->marca) ?></td>
                                </tr>
                                <tr>
                                    <th>Modelo:</th>
                                    <td><?= htmlspecialchars($producto->modelo) ?></td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <!-- Visualización de imagen actual -->
                    <div class="text-center mb-4">
                        <p class="text-muted">Imagen actual:</p>
                        <img src="<?= htmlspecialchars($producto->imagen) ?>"
                             width="200"
                             alt="Producto"
                             class="img-thumbnail">
                    </div>

                    <!-- Formulario de subida de imagen -->
                    <form method="POST"
                          action="update_image_file.php?id=<?= $producto->id ?>"
                          enctype="multipart/form-data">
                        <div class="mb-3">
                            <label for="imagen" class="form-label">
                                Seleccionar nueva imagen (JPG o PNG)
                            </label>
                            <input type="file"
                                   class="form-control"
                                   id="imagen"
                                   name="imagen"
                                   accept="image/jpeg,image/png"
                                   required>
                            <div class="form-text">
                                Formatos permitidos: JPG, PNG. Tamaño máximo: 2MB
                            </div>
                        </div>

                        <!-- Botones de acción -->
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                Actualizar Imagen
                            </button>
                            <a href="/tienda-online/src/index.php" class="btn btn-secondary">
                                Cancelar
                            </a>
                            <a href="/tienda-online/src/index.php" class="btn btn-secondary">
                                Volver
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

<?php require_once __DIR__ . '/footer.php'; ?>