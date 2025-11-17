<?php
/**
 * Servicio de gestión de Sesión (Patrón Singleton)
 *
 * Centraliza toda la lógica de sesión de usuario: inicio, cierre, autenticación,
 * control de visitas y roles. Garantiza una única instancia por petición.
 */
namespace services;

class SessionService
{
    // Instancia única del servicio (patrón Singleton)
    private static $instance;

    // Tiempo de expiración de la sesión por inactividad (1 hora)
    private $expireAfterSeconds = 3600;

    // Constructor privado: impide creación directa de instancias
    // Inicializa la sesión y establece valores por defecto
    private function __construct()
    {
        // Inicia la sesión PHP si no está activa
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Verifica si la sesión ha expirado por inactividad
        $this->checkSessionExpiration();

        // Inicializa variables de sesión si es la primera vez
        if (!isset($_SESSION['loggedIn'])) {
            $_SESSION['loggedIn'] = false;
            $_SESSION['isAdmin'] = false;
            $_SESSION['username'] = 'Invitado';
            $_SESSION['visits'] = 0;
        }

        // Actualiza tiempo de última actividad e incrementa contador de visitas
        $_SESSION['lastActivity'] = time();
        $_SESSION['visits']++;
    }

    // Obtiene la única instancia del servicio (patrón Singleton)
    // Crea la instancia en la primera llamada, luego devuelve la existente
    public static function getInstance(): SessionService
    {
        if (!isset(self::$instance)) {
            self::$instance = new SessionService();
        }
        return self::$instance;
    }

    // Verifica si la sesión ha expirado por inactividad
    // Si ha pasado más de 1 hora sin actividad, cierra la sesión
    private function checkSessionExpiration()
    {
        if (isset($_SESSION['lastActivity'])) {
            if (time() - $_SESSION['lastActivity'] > $this->expireAfterSeconds) {
                $this->logout();
            }
        }
    }

    // Inicia sesión para un usuario autenticado
    // Establece variables de sesión con datos del usuario y roles
    public function login($user)
    {
        $_SESSION['loggedIn'] = true;
        $_SESSION['userId'] = $user->id;
        $_SESSION['username'] = $user->username;
        $_SESSION['isAdmin'] = in_array('ADMIN', $user->roles);
        $_SESSION['loginTime'] = date('Y-m-d H:i:s');
    }

    // Cierra la sesión del usuario
    // Elimina todos los datos de sesión y destruye la sesión
    public function logout()
    {
        session_unset();
        session_destroy();
    }

    // Verifica si hay un usuario autenticado
    // Retorna true si el usuario ha iniciado sesión
    public function isLoggedIn(): bool
    {
        return $_SESSION['loggedIn'] ?? false;
    }

    // Verifica si el usuario actual tiene rol de administrador
    // Retorna true si el usuario es ADMIN
    public function isAdmin(): bool
    {
        return $_SESSION['isAdmin'] ?? false;
    }

    // Obtiene el nombre del usuario actual
    // Retorna el username o 'Invitado' si no hay sesión
    public function getUsername(): string
    {
        return $_SESSION['username'] ?? 'Invitado';
    }

    // Obtiene el número de visitas del usuario en la sesión actual
    public function getVisits(): int
    {
        return $_SESSION['visits'] ?? 0;
    }

    // Obtiene la fecha y hora del último login
    // Retorna string con la fecha o null si no hay login
    public function getLoginTime(): ?string
    {
        return $_SESSION['loginTime'] ?? null;
    }
}