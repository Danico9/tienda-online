<?php
/**
 * Modelo de datos para la entidad Usuario
 *
 * Representa un usuario del sistema con sus credenciales y roles.
 * Gestiona autenticación y autorización mediante sistema de roles (USER, ADMIN).
 */
namespace models;

class User
{
    // Identificador numérico único del usuario
    public $id;

    // Nombre de usuario para login (único)
    public $username;

    // Contraseña hasheada con bcrypt (nunca en texto plano)
    public $password;

    // Nombre del usuario
    public $nombre;

    // Apellidos del usuario
    public $apellidos;

    // Correo electrónico (único)
    public $email;

    // Array de roles asignados (ej: ['USER'], ['ADMIN'])
    public $roles = [];

    // Fecha de registro del usuario
    public $createdAt;

    // Fecha de última actualización
    public $updatedAt;

    // Marca de borrado lógico (false = activo, true = desactivado)
    public $isDeleted;

    // Constructor: inicializa el usuario con todos sus atributos
    // El array de roles permite gestionar múltiples roles simultáneamente
    public function __construct(
        $id = null,
        $username = null,
        $password = null,
        $nombre = null,
        $apellidos = null,
        $email = null,
        $roles = [],
        $createdAt = null,
        $updatedAt = null,
        $isDeleted = false
    )
    {
        $this->id = $id;
        $this->username = $username;
        $this->password = $password;
        $this->nombre = $nombre;
        $this->apellidos = $apellidos;
        $this->email = $email;
        $this->roles = $roles;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
        $this->isDeleted = $isDeleted;
    }
}