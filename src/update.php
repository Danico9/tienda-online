<?php
/**
 * Formulario de edición de productos
 *
 * Permite a los administradores modificar los datos de un producto existente.
 * Incluye validación de campos y mantiene la imagen actual.
 * Acceso restringido: solo administradores.
 */

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/services/SessionService.php';
require_once __DIR__ . '/services/ProductosService.php';
require_once __DIR__ . '/services/CategoriasService.php';
require_once __DIR__ . '/config/Config.php';

use services\SessionService;
use services\ProductosService;
use services\CategoriasService;
use config\Config;

$session = SessionService::getInstance();

// Control de acceso: solo administradores pueden editar productos
if (!$session->isAdmin()) {
    header('Location: /tienda-online/src/index.php');
    exit;
}

// Inicializa servicios necesarios
$config = Config::getInstance();
$productosService = new ProductosService($config->db);
$categoriasService = new CategoriasService($config->db);

// Valida que se reciba un ID válido
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id) {
    header('Location: /tienda-online/src/index.php');
    exit;
}

// Busca el producto a editar
$producto = $productosService->findById($id);
if (!$producto) {
    header('Location: /tienda-online/src/index.php');
    exit;
}

$errors = [];
$success = false;

// Procesa el formulario al enviar
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitiza y valida los datos recibidos
    $marca = filter_input(INPUT_POST, 'marca', FILTER_SANITIZE_STRING);
    $modelo = filter_input(INPUT_POST, 'modelo', FILTER_SANITIZE_STRING);
    $descripcion = filter_input(INPUT_POST, 'descripcion', FILTER_SANITIZE_STRING);
    $precio = filter_input(INPUT_POST, 'precio', FILTER_VALIDATE_FLOAT);
    $stock = filter_input(INPUT_POST, 'stock', FILTER_VALIDATE_INT);
    $categoriaId = filter_input(INPUT_POST, 'categoria_id', FILTER_SANITIZE_STRING);

    // Validaciones de campos obligatorios
    if (empty($marca)) {
        $errors['marca'] = 'La marca es obligatoria';
    }
    if (empty($modelo)) {
        $errors['modelo'] = 'El modelo es obligatorio';
    }
    if ($precio === false || $precio <= 0) {
        $errors['precio'] = 'El precio debe ser mayor que 0';
    }
    if ($stock === false || $stock < 0) {
        $errors['stock'] = 'El stock no puede ser negativo';
    }

    // Si no hay errores, actualiza el producto
    if (empty($errors)) {
        $producto->marca = $marca;
        $producto->modelo = $modelo;
        $producto->descripcion = $descripcion;
        $producto->precio = $precio;
        $producto->stock = $stock;
        $producto->categoriaId = $categoriaId;

        if ($productosService->update($producto)) {
            $success = true;
            header('Location: /tienda-online/src/index.php');
            exit;
        } else {
            $errors['general'] = 'Error al actualizar el producto';
        }
    }
}

// Obtiene todas las categorías para el select del formulario
$categorias = $categoriasService->findAll();

require_once __DIR__ . '/header.php';
?>

    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card shadow">
                <div class="card-header bg-warning text-dark">
                    <h2 class="mb-0">Editar Producto #<?= $producto->id ?></h2>
                </div>
                <div class="card-body">
                    <?php if (isset($errors['general'])): ?>
                        <div class="alert alert-danger">
                            <?= $errors['general'] ?>
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="update.php?id=<?= $producto->id ?>">
                        <!-- Campo: Marca -->
                        <div class="mb-3">
                            <label for="marca" class="form-label">Marca *</label>
                            <input type="text"
                                   class="form-control <?= isset($errors['marca']) ? 'is-invalid' : '' ?>"
                                   id="marca"
                                   name="marca"
                                   value="<?= htmlspecialchars($_POST['marca'] ?? $producto->marca) ?>"
                                   required>
                            <?php if (isset($errors['marca'])): ?>
                                <div class="invalid-feedback"><?= $errors['marca'] ?></div>
                            <?php endif; ?>
                        </div>

                        <!-- Campo: Modelo -->
                        <div class="mb-3">
                            <label for="modelo" class="form-label">Modelo *</label>
                            <input type="text"
                                   class="form-control <?= isset($errors['modelo']) ? 'is-invalid' : '' ?>"
                                   id="modelo"
                                   name="modelo"
                                   value="<?= htmlspecialchars($_POST['modelo'] ?? $producto->modelo) ?>"
                                   required>
                            <?php if (isset($errors['modelo'])): ?>
                                <div class="invalid-feedback"><?= $errors['modelo'] ?></div>
                            <?php endif; ?>
                        </div>

                        <!-- Campo: Descripción -->
                        <div class="mb-3">
                            <label for="descripcion" class="form-label">Descripción *</label>
                            <textarea class="form-control <?= isset($errors['descripcion']) ? 'is-invalid' : '' ?>"
                                      id="descripcion"
                                      name="descripcion"
                                      rows="3"
                                      required><?= htmlspecialchars($_POST['descripcion'] ?? $producto->descripcion) ?></textarea>
                            <?php if (isset($errors['descripcion'])): ?>
                                <div class="invalid-feedback"><?= $errors['descripcion'] ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="row">
                            <!-- Campo: Precio -->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="precio" class="form-label">Precio ($) *</label>
                                    <input type="number"
                                           step="0.01"
                                           class="form-control <?= isset($errors['precio']) ? 'is-invalid' : '' ?>"
                                           id="precio"
                                           name="precio"
                                           value="<?= htmlspecialchars($_POST['precio'] ?? $producto->precio) ?>"
                                           required>
                                    <?php if (isset($errors['precio'])): ?>
                                        <div class="invalid-feedback"><?= $errors['precio'] ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <!-- Campo: Stock -->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="stock" class="form-label">Stock *</label>
                                    <input type="number"
                                           class="form-control <?= isset($errors['stock']) ? 'is-invalid' : '' ?>"
                                           id="stock"
                                           name="stock"
                                           value="<?= htmlspecialchars($_POST['stock'] ?? $producto->stock) ?>"
                                           required>
                                    <?php if (isset($errors['stock'])): ?>
                                        <div class="invalid-feedback"><?= $errors['stock'] ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <!-- Visualización de imagen actual -->
                        <div class="mb-3">
                            <label class="form-label">Imagen Actual</label>
                            <div>
                                <img src="<?= htmlspecialchars($producto->imagen) ?>"
                                     alt="Producto"
                                     width="150"
                                     class="img-thumbnail">
                                <p class="text-muted small mt-2">
                                    Para cambiar la imagen, usa el botón "Cambiar Imagen" después de actualizar
                                </p>
                            </div>
                        </div>

                        <!-- Campo: Categoría -->
                        <div class="mb-3">
                            <label for="categoria_id" class="form-label">Categoría *</label>
                            <select class="form-select <?= isset($errors['categoria_id']) ? 'is-invalid' : '' ?>"
                                    id="categoria_id"
                                    name="categoria_id"
                                    required>
                                <option value="">Seleccione una categoría</option>
                                <?php foreach ($categorias as $categoria): ?>
                                    <option value="<?= $categoria->id ?>"
                                            <?= ($producto->categoriaId == $categoria->id) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($categoria->nombre) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <?php if (isset($errors['categoria_id'])): ?>
                                <div class="invalid-feedback"><?= $errors['categoria_id'] ?></div>
                            <?php endif; ?>
                        </div>

                        <!-- Botones de acción -->
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-warning">
                                Actualizar Producto
                            </button>
                            <a href="update-image.php?id=<?= $producto->id ?>" class="btn btn-secondary">
                                Cambiar Imagen
                            </a>
                            <a href="/tienda-online/src/index.php" class="btn btn-secondary">
                                Cancelar
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

<?php require_once __DIR__ . '/footer.php'; ?>