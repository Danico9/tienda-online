<?php
/**
 * Servicio de gestión de Categorías
 *
 * Actúa como intermediario entre la base de datos y la aplicación
 * para todas las operaciones relacionadas con categorías de productos.
 */
namespace services;

use models\Categoria;
use PDO;

require_once __DIR__ . '/../models/Categoria.php';

class CategoriasService
{
    // Conexión PDO a la base de datos
    private $pdo;

    // Constructor: recibe y almacena la conexión PDO
    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    // Recupera todas las categorías ordenadas alfabéticamente por nombre
    // Retorna un array de objetos Categoria
    public function findAll()
    {
        // Prepara consulta SQL
        $stmt = $this->pdo->prepare("SELECT * FROM categorias ORDER BY nombre ASC");

        // Ejecuta la consulta
        $stmt->execute();

        // Preparar array para guardar resultados
        $categorias = [];
        // Convierte cada fila en un objeto Categoria
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $categoria = new Categoria(
                $row['id'],
                $row['nombre'],
                $row['created_at'],
                $row['updated_at'],
                $row['is_deleted']
            );
            // Agregar el objeto al array
            $categorias[] = $categoria;
        }

        // Se devuelve el array de objetos categoria
        return $categorias;
    }

    // Busca una categoría por su nombre exacto
    // Retorna objeto Categoria si existe, false si no se encuentra
    public function findByName($name)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM categorias WHERE nombre = :nombre");
        $stmt->execute(['nombre' => $name]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) {
            return false;
        }

        return new Categoria(
            $row['id'],
            $row['nombre'],
            $row['created_at'],
            $row['updated_at'],
            $row['is_deleted']
        );
    }
}