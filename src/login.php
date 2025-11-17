<?php
/**
 * Página de inicio de sesión
 *
 * Formulario de autenticación para usuarios del sistema.
 * Valida credenciales y establece sesión con roles correspondientes.
 * Incluye tabla de usuarios de prueba para facilitar testing.
 */

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/services/SessionService.php';
require_once __DIR__ . '/services/UsersService.php';
require_once __DIR__ . '/config/Config.php';

use services\SessionService;
use services\UsersService;
use config\Config;

$session = SessionService::getInstance();

// Si ya está logueado, redirige al índice
if ($session->isLoggedIn()) {
    header('Location: /tienda-online/src/index.php');
    exit;
}

$error = '';

// Procesa el formulario de login
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
    $password = $_POST['password'] ?? '';

    // Valida que los campos no estén vacíos
    if (empty($username) || empty($password)) {
        $error = 'Por favor, complete todos los campos';
    } else {
        try {
            $config = Config::getInstance();
            $usersService = new UsersService($config->db);

            // Autentica usuario y establece sesión
            $user = $usersService->authenticate($username, $password);
            $session->login($user);

            header('Location: /tienda-online/src/index.php');
            exit;
        } catch (Exception $e) {
            $error = $e->getMessage();
        }
    }
}

require_once __DIR__ . '/header.php';
?>

    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-4">
            <div class="card shadow">
                <div class="card-header bg-primary text-white text-center">
                    <h4 class="mb-0">Iniciar Sesión</h4>
                </div>
                <div class="card-body">
                    <!-- Mensaje de error si la autenticación falla -->
                    <?php if (!empty($error)): ?>
                        <div class="alert alert-danger" role="alert">
                            <?= htmlspecialchars($error) ?>
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="login.php">
                        <!-- Campo: Usuario -->
                        <div class="mb-3">
                            <label for="username" class="form-label">Usuario</label>
                            <input type="text"
                                   class="form-control"
                                   id="username"
                                   name="username"
                                   value="<?= htmlspecialchars($_POST['username'] ?? '') ?>"
                                   required
                                   autofocus>
                        </div>

                        <!-- Campo: Contraseña -->
                        <div class="mb-3">
                            <label for="password" class="form-label">Contraseña</label>
                            <input type="password"
                                   class="form-control"
                                   id="password"
                                   name="password"
                                   required>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">
                                Iniciar Sesión
                            </button>
                        </div>
                    </form>

                    <hr>

                    <!-- Tabla de usuarios de prueba para testing -->
                    <div class="text-center">
                        <small class="text-muted">Usuarios de prueba:</small>
                        <table class="table table-sm mt-2">
                            <thead>
                            <tr>
                                <th>Usuario</th>
                                <th>Contraseña</th>
                                <th>Rol</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td><code>admin</code></td>
                                <td><code>Admin1</code></td>
                                <td><span class="badge bg-danger">Admin</span></td>
                            </tr>
                            <tr>
                                <td><code>user</code></td>
                                <td><code>User1234</code></td>
                                <td><span class="badge bg-info">Usuario</span></td>
                            </tr>
                            <tr>
                                <td><code>test</code></td>
                                <td><code>test1234</code></td>
                                <td><span class="badge bg-info">Usuario</span></td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="text-center mt-3">
                <a href="index.php" class="btn btn-link">Volver al inicio</a>
            </div>
        </div>
    </div>

<?php require_once __DIR__ . '/footer.php'; ?>