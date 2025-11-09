<?php
namespace config;

use Dotenv\Dotenv;
use PDO;
use PDOException;

class Config
{
    private static $instance;

    // Propiedades de configuración
    private $dbConnection;
    private $dbHost;
    private $dbPort;
    private $dbDatabase;
    private $dbUsername;
    private $dbPassword;

    private $uploadPath;
    private $uploadUrl;
    private $rootPath;

    private $db;

    private function __construct()
    {
        // 1. Cargar variables de entorno desde .env
        $this->loadEnv();

        // 2. Asignar valores desde .env a propiedades
        $this->dbConnection = $_ENV['DB_CONNECTION'] ?? 'mysql';
        $this->dbHost = $_ENV['DB_HOST'] ?? 'localhost';
        $this->dbPort = $_ENV['DB_PORT'] ?? '3306';
        $this->dbDatabase = $_ENV['DB_DATABASE'] ?? 'tienda';
        $this->dbUsername = $_ENV['DB_USERNAME'] ?? 'root';
        $this->dbPassword = $_ENV['DB_PASSWORD'] ?? 'secret';

        $this->uploadPath = $_ENV['UPLOAD_PATH'] ?? 'C:/xampp/htdocs/tienda-online/src/uploads/';
        $this->uploadUrl = $_ENV['UPLOAD_URL'] ?? 'http://localhost/tienda-online/src/uploads/';
        $this->rootPath = dirname(__DIR__, 2) . '/';

        // 3. Conectar a la base de datos
        $this->connectDatabase();
    }

    /**
     * Cargar archivo .env usando Dotenv
     */
    private function loadEnv()
    {
        try {
            // Buscar el .env en la raíz del proyecto
            $rootPath = dirname(__DIR__, 2);

            if (file_exists($rootPath . '/.env')) {
                // Cargar .env con Dotenv
                $dotenv = Dotenv::createImmutable($rootPath);
                $dotenv->load();
            } else {
                // Si no existe .env, usar valores por defecto
                $_ENV['DB_HOST'] = 'localhost';
                $_ENV['DB_DATABASE'] = 'tienda';
                $_ENV['DB_USERNAME'] = 'root';
                $_ENV['DB_PASSWORD'] = '';
            }
        } catch (\Exception $e) {
            die("Error cargando archivo .env: " . $e->getMessage());
        }
    }

    /**
     * Conectar a la base de datos
     */
    private function connectDatabase()
    {
        try {
            $dsn = "{$this->dbConnection}:host={$this->dbHost};port={$this->dbPort};dbname={$this->dbDatabase};charset=utf8mb4";

            $this->db = new PDO(
                $dsn,
                $this->dbUsername,
                $this->dbPassword,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false
                ]
            );
        } catch (PDOException $e) {
            die("Error de conexión a la base de datos: " . $e->getMessage() .
                "<br><br>Verifica tu archivo .env o la configuración de MySQL en XAMPP");
        }
    }

    /**
     * Patrón Singleton - solo una instancia
     */
    public static function getInstance(): Config
    {
        if (!isset(self::$instance)) {
            self::$instance = new Config();
        }
        return self::$instance;
    }

    /**
     * Métodos mágicos para acceder a propiedades
     */
    public function __get($name)
    {
        return $this->$name ?? null;
    }

    public function __set($name, $value)
    {
        $this->$name = $value;
    }

    /**
     * Métodos auxiliares
     */
    public function getEnv($key, $default = null)
    {
        return $_ENV[$key] ?? $default;
    }

    public function isProduction(): bool
    {
        return ($_ENV['APP_ENV'] ?? 'production') === 'production';
    }

    public function isDevelopment(): bool
    {
        return ($_ENV['APP_ENV'] ?? 'development') === 'development';
    }
}