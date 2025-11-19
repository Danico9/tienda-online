<?php
/**
 * Formulario de creación de productos
 *
 * Permite a los administradores añadir nuevos productos al catálogo.
 * Incluye validación de campos y gestión de errores.
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
use models\Producto;

$session = SessionService::getInstance();

// Control de acceso: solo administradores pueden crear productos
if (!$session->isAdmin()) {
    // Redirigimos a inicio
    header('Location: /tienda-online/src/index.php');
    exit;
}

// Inicializa servicios necesarios
$config = Config::getInstance();
$productosService = new ProductosService($config->db);
$categoriasService = new CategoriasService($config->db);

$errors = [];
$success = false;

// Procesa el formulario al enviar
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitiza y valida los datos recibidos
    //                              Origen         Campo            Filtro
    $marca = filter_input(INPUT_POST, 'marca', FILTER_SANITIZE_STRING);
    $modelo = filter_input(INPUT_POST, 'modelo', FILTER_SANITIZE_STRING);
    $descripcion = filter_input(INPUT_POST, 'descripcion', FILTER_SANITIZE_STRING);
    $precio = filter_input(INPUT_POST, 'precio', FILTER_VALIDATE_FLOAT);
    $stock = filter_input(INPUT_POST, 'stock', FILTER_VALIDATE_INT);
    $categoriaId = filter_input(INPUT_POST, 'categoria_id', FILTER_SANITIZE_STRING);
    $imagen = 'https://via.placeholder.com/150'; // Imagen por defecto

    // Validaciones de campos obligatorios
    if (empty($marca)) {
        $errors['marca'] = 'La marca es obligatoria';
    }
    if (empty($modelo)) {
        $errors['modelo'] = 'El modelo es obligatorio';
    }
    if (empty($descripcion)) {
        $errors['descripcion'] = 'La descripción es obligatoria';
    }
    if ($precio === false || $precio <= 0) {
        $errors['precio'] = 'El precio debe ser mayor que 0';
    }
    if ($stock === false || $stock < 0) {
        $errors['stock'] = 'El stock no puede ser negativo';
    }
    if (empty($categoriaId)) {
        $errors['categoria_id'] = 'Debe seleccionar una categoría';
    }

    // Si no hay errores, crea y guarda el producto
    if (empty($errors)) {
        $producto = new Producto(
                null, null, $marca, $modelo, $descripcion,
                $precio, $stock, $imagen, $categoriaId
        );

        if ($productosService->save($producto)) {
            $success = true;
            header('Location: /tienda-online/src/index.php');
            exit;
        } else {
            $errors['general'] = 'Error al guardar el producto';
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
                <div class="card-header bg-success text-white">
                    <h2 class="mb-0">Crear Nuevo Producto</h2>
                </div>
                <div class="card-body">
                    <?php if (isset($errors['general'])): ?>
                        <div class="alert alert-danger">
                            <?= $errors['general'] ?>
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="create.php">
                        <!-- Campo: Marca -->
                        <div class="mb-3">
                            <label for="marca" class="form-label">Marca *</label>
                            <input type="text"
                                   class="form-control <?= isset($errors['marca']) ? 'is-invalid' : '' ?>"
                                   id="marca"
                                   name="marca"
                                   value="<?= htmlspecialchars($_POST['marca'] ?? '') ?>"
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
                                   value="<?= htmlspecialchars($_POST['modelo'] ?? '') ?>"
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
                                      required><?= htmlspecialchars($_POST['descripcion'] ?? '') ?></textarea>
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
                                           value="<?= htmlspecialchars($_POST['precio'] ?? '') ?>"
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
                                           value="<?= htmlspecialchars($_POST['stock'] ?? '') ?>"
                                           required>
                                    <?php if (isset($errors['stock'])): ?>
                                        <div class="invalid-feedback"><?= $errors['stock'] ?></div>
                                    <?php endif; ?>
                                </div>
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
                                            <?= (isset($_POST['categoria_id']) && $_POST['categoria_id'] == $categoria->id) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($categoria->nombre) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <?php if (isset($errors['categoria_id'])): ?>
                                <div class="invalid-feedback"><?= $errors['categoria_id'] ?></div>
                            <?php endif; ?>
                        </div>

                        <!-- Nota informativa sobre la imagen -->
                        <div class="alert alert-info">
                            <small>La imagen se establecerá por defecto. Podrás cambiarla después de crear el producto.</small>
                        </div>

                        <!-- Botones de acción -->
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-success">
                                Crear Producto
                            </button>
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