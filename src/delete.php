<?php
/**
 * Script de eliminación de productos
 *
 * Elimina un producto y su imagen asociada del sistema.
 * Acceso restringido: solo administradores.
 * Incluye limpieza de archivos huérfanos.
 */

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/services/SessionService.php';
require_once __DIR__ . '/services/ProductosService.php';
require_once __DIR__ . '/config/Config.php';

use services\SessionService;
use services\ProductosService;
use config\Config;

$session = SessionService::getInstance();

// Control de acceso: solo administradores pueden eliminar productos
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

// Inicializar servicios
$config = Config::getInstance();
$productosService = new ProductosService($config->db);

// Obtiene el producto para eliminar su imagen física
$producto = $productosService->findById($id);

if ($producto) {
    // Elimina la imagen física del servidor si no es la por defecto
    // strpos devuelve 0 si la subcadena no está al principio ( es decir, que no encuentra placeholder.com) y por lo tanto extraería con
    // basename el nombre del archivo en la ruta con imagePath, y luego verifica que existe y lo elimina con unlink
    if ($producto->imagen && strpos($producto->imagen, 'placeholder.com') === false) {
        $imagePath = $config->uploadPath . basename($producto->imagen);
        if (file_exists($imagePath)) {
            unlink($imagePath);
        }
    }

    // Elimina el producto de la base de datos
    if ($productosService->deleteById($id)) {
        header('Location: /tienda-online/src/index.php?deleted=1');
        exit;
    }
}

// Si algo falló, redirige con mensaje de error
header('Location: /tienda-online/src/index.php?error=1');
exit;