<?php
/**
 * Cabecera de la aplicación (Header)
 *
 * Incluida en todas las páginas del sistema.
 * Contiene navbar con navegación y gestión de sesión.
 * Muestra opciones diferentes según el rol del usuario.
 */

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/services/SessionService.php';

use services\SessionService;

// Obtiene información de la sesión actual
$session = SessionService::getInstance();
$username = $session->getUsername();
$isLoggedIn = $session->isLoggedIn();
$isAdmin = $session->isAdmin();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tienda Online - CRUD Productos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<!-- Barra de navegación principal -->
<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container-fluid">
        <a class="navbar-brand" href="/tienda-online/src/index.php">
            Tienda Online
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <!-- Menú izquierdo -->
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link" href="/tienda-online/src/index.php">Productos</a>
                </li>
                <!-- Opción exclusiva para administradores -->
                <?php if ($isAdmin): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="/tienda-online/src/create.php">Nuevo Producto</a>
                    </li>
                <?php endif; ?>
            </ul>

            <!-- Menú derecho: info de usuario y login/logout -->
            <ul class="navbar-nav">
                <li class="nav-item">
                        <span class="navbar-text me-3">
                            Usuario: <strong><?= htmlspecialchars($username) ?></strong>
                            <?php if ($isAdmin): ?>
                                <span class="badge bg-danger">ADMIN</span>
                            <?php endif; ?>
                        </span>
                </li>
                <li class="nav-item">
                    <?php if ($isLoggedIn): ?>
                        <a class="btn btn-outline-light btn-sm" href="/tienda-online/src/logout.php">Cerrar Sesión</a>
                    <?php else: ?>
                        <a class="btn btn-outline-light btn-sm" href="/tienda-online/src/login.php">Iniciar Sesión</a>
                    <?php endif; ?>
                </li>
            </ul>
        </div>
    </div>
</nav>

<!-- Container principal (cerrado en footer.php) -->
<div class="container mt-4">