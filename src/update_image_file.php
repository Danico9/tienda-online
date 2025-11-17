<?php
/**
 * Procesador de actualización de imagen de producto
 *
 * Maneja la subida, validación y almacenamiento de la nueva imagen.
 * Elimina la imagen anterior y actualiza la base de datos.
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

// Verifica que sea POST con archivo adjunto
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_FILES['imagen'])) {
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

$file = $_FILES['imagen'];

// Valida que no hay errores en la subida del archivo
if ($file['error'] !== UPLOAD_ERR_OK) {
    header('Location: update-image.php?id=' . $id . '&error=upload_error');
    exit;
}

// Valida tamaño máximo de 2MB
if ($file['size'] > 2 * 1024 * 1024) {
    header('Location: update-image.php?id=' . $id . '&error=file_too_large');
    exit;
}

// Valida que el archivo es una imagen JPG o PNG
$allowedTypes = ['image/jpeg', 'image/png'];
$fileType = mime_content_type($file['tmp_name']);

if (!in_array($fileType, $allowedTypes)) {
    header('Location: update-image.php?id=' . $id . '&error=invalid_type');
    exit;
}

// Genera nombre único usando el UUID del producto
$extension = ($fileType === 'image/jpeg') ? 'jpg' : 'png';
$newFileName = $producto->uuid . '.' . $extension;
$uploadPath = $config->uploadPath . $newFileName;
$uploadUrl = $config->uploadUrl . $newFileName;

// Crea directorio de uploads si no existe
if (!is_dir($config->uploadPath)) {
    mkdir($config->uploadPath, 0755, true);
}

// Elimina imagen anterior si existe y no es la imagen por defecto
if ($producto->imagen && strpos($producto->imagen, 'placeholder.com') === false) {
    $oldImagePath = $config->uploadPath . basename($producto->imagen);
    if (file_exists($oldImagePath)) {
        unlink($oldImagePath);
    }
}

// Mueve el archivo subido a su ubicación final
if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
    // Actualiza la URL de la imagen en la base de datos
    $producto->imagen = $uploadUrl;
    $productosService->update($producto);

    header('Location: update-image.php?id=' . $id . '&success=1');
    exit;
}

// Si falló la subida, redirige con error
header('Location: update-image.php?id=' . $id . '&error=upload_failed');
exit;