<?php
/**
 * Servicio de gestión de Usuarios
 *
 * Gestiona la autenticación de usuarios y consulta de datos relacionados.
 * Incluye verificación de contraseñas y gestión de roles.
 */
namespace services;

use Exception;
use models\User;
use PDO;

require_once __DIR__ . '/../models/User.php';

class UsersService
{
    // Conexión PDO a la base de datos
    private $db;

    // Constructor: recibe y almacena la conexión PDO
    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    // Autentica un usuario por username y password
    // Verifica la contraseña usando password_verify() para hashes bcrypt
    // Retorna objeto User si las credenciales son válidas
    // Lanza Exception si las credenciales son incorrectas
    public function authenticate($username, $password): User
    {
        // Buscar usuario por username
        $user = $this->findUserByUsername($username);

        // Verifica que el usuario existe y la contraseña es correcta
        if (!$user || !password_verify($password, $user->password)) {
            throw new Exception("Credenciales inválidas");
        }

        return $user;
    }

    // Busca un usuario por su nombre de usuario
    // Recupera también los roles asociados mediante JOIN con user_roles
    // Retorna objeto User con todos sus datos y roles, o null si no existe
    private function findUserByUsername($username): ?User
    {
        $stmt = $this->db->prepare("
            SELECT u.*, GROUP_CONCAT(ur.roles) as roles
            FROM usuarios u
            LEFT JOIN user_roles ur ON u.id = ur.user_id
            WHERE u.username = :username
            GROUP BY u.id
        ");

        $stmt->bindParam(':username', $username, PDO::PARAM_STR);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        // Usuario no existe
        if (!$row) {
            return null;
        }

        // Convierte la cadena de roles separados por comas en array
        $roles = $row['roles'] ? explode(',', $row['roles']) : [];

        return new User(
            $row['id'],
            $row['username'],
            $row['password'],
            $row['nombre'],
            $row['apellidos'],
            $row['email'],
            $roles,
            $row['created_at'],
            $row['updated_at'],
            $row['is_deleted']
        );
    }
}