<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/config/Config.php';

use config\Config;

echo "<h1>🧪 Test de Configuración</h1>";

try {
    $config = Config::getInstance();

    echo "<div style='background: #d4edda; padding: 20px; border-radius: 5px;'>";
    echo "<h2>✅ Conexión exitosa a la base de datos</h2>";
    echo "<table border='1' cellpadding='10' style='border-collapse: collapse;'>";
    echo "<tr><th>Variable</th><th>Valor</th></tr>";
    echo "<tr><td>Base de datos</td><td>" . $config->dbDatabase . "</td></tr>";
    echo "<tr><td>Host</td><td>" . $config->dbHost . "</td></tr>";
    echo "<tr><td>Puerto</td><td>" . $config->dbPort . "</td></tr>";
    echo "<tr><td>Usuario</td><td>" . $config->dbUsername . "</td></tr>";
    echo "<tr><td>Modo</td><td>" . ($config->isDevelopment() ? '🔧 Desarrollo' : '🚀 Producción') . "</td></tr>";
    echo "<tr><td>Ruta uploads</td><td>" . $config->uploadPath . "</td></tr>";
    echo "<tr><td>URL uploads</td><td>" . $config->uploadUrl . "</td></tr>";
    echo "</table>";
    echo "</div>";

    echo "<br>";
    echo "<h2>📋 Variables de entorno cargadas desde .env:</h2>";
    echo "<pre style='background: #f8f9fa; padding: 15px; border-radius: 5px;'>";
    echo "DB_HOST: " . ($_ENV['DB_HOST'] ?? 'No definida') . "\n";
    echo "DB_DATABASE: " . ($_ENV['DB_DATABASE'] ?? 'No definida') . "\n";
    echo "DB_USERNAME: " . ($_ENV['DB_USERNAME'] ?? 'No definida') . "\n";
    echo "APP_ENV: " . ($_ENV['APP_ENV'] ?? 'No definida') . "\n";
    echo "</pre>";

} catch (Exception $e) {
    echo "<div style='background: #f8d7da; padding: 20px; border-radius: 5px; color: #721c24;'>";
    echo "<h2>❌ Error:</h2>";
    echo "<p>" . $e->getMessage() . "</p>";
    echo "</div>";
}


// **Abrir en navegador:**
// http://localhost/tienda-online/src/test.php

