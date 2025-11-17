<?php
/**
 * Script de cierre de sesión
 *
 * Destruye la sesión actual del usuario y redirige al inicio.
 * Limpia todas las variables de sesión almacenadas.
 */

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/services/SessionService.php';

use services\SessionService;

// Obtiene instancia de sesión y ejecuta logout
$session = SessionService::getInstance();
$session->logout();

// Redirige al índice tras cerrar sesión
header('Location: /tienda-online/src/index.php');
exit;