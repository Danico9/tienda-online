<?php
/**
 * Servicio de gestión de Productos
 *
 * Centraliza toda la lógica de operaciones CRUD sobre productos.
 * Incluye búsquedas, relaciones con categorías y gestión de imágenes.
 */
namespace services;

use models\Producto;
use PDO;
use Ramsey\Uuid\Uuid;

require_once __DIR__ . '/../models/Producto.php';

class ProductosService
{
    // Conexión PDO a la base de datos
    private $pdo;

    // Constructor: recibe y almacena la conexión PDO
    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    // Recupera todos los productos con su categoría asociada mediante JOIN
    // Parámetro $searchTerm: filtra por marca o modelo (opcional)
    // Retorna array de objetos Producto
    public function findAllWithCategoryName($searchTerm = '')
    {
        $sql = "
        SELECT p.*, c.nombre as categoria_nombre
        FROM productos p
        LEFT JOIN categorias c ON p.categoria_id = c.id
    ";

        $params = [];

        // Aplica filtro de búsqueda si se proporciona término
        if (!empty($searchTerm)) {
            $searchParam = '%' . strtolower($searchTerm) . '%';
            $sql .= " WHERE (LOWER(p.marca) LIKE ? OR LOWER(p.modelo) LIKE ?)";
            $params[] = $searchParam;
            $params[] = $searchParam;  // Mismo valor para marca y modelo
        }

        $sql .= " ORDER BY p.id ASC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        $productos = [];
        // Convierte cada fila en un objeto Producto
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $productos[] = new Producto(
                $row['id'],
                $row['uuid'],
                $row['marca'],
                $row['modelo'],
                $row['descripcion'],
                $row['precio'],
                $row['stock'],
                $row['imagen'],
                $row['categoria_id'],
                $row['categoria_nombre'] ?? null,
                $row['created_at'],
                $row['updated_at'],
                $row['is_deleted']
            );
        }

        return $productos;
    }

    // Busca un producto específico por su ID con información de categoría
    // Retorna objeto Producto si existe, null si no se encuentra
    public function findById($id)
    {
        $stmt = $this->pdo->prepare("
            SELECT p.*, c.nombre as categoria_nombre
            FROM productos p
            LEFT JOIN categorias c ON p.categoria_id = c.id
            WHERE p.id = :id
        ");

        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) {
            return null;
        }

        return new Producto(
            $row['id'],
            $row['uuid'],
            $row['marca'],
            $row['modelo'],
            $row['descripcion'],
            $row['precio'],
            $row['stock'],
            $row['imagen'],
            $row['categoria_id'],
            $row['categoria_nombre'],
            $row['created_at'],
            $row['updated_at'],
            $row['is_deleted']
        );
    }

    // Inserta un nuevo producto en la base de datos
    // Genera automáticamente UUID único y establece fechas de creación
    // Retorna true si la operación fue exitosa
    public function save(Producto $producto)
    {
        $uuid = Uuid::uuid4()->toString();
        $now = date('Y-m-d H:i:s');

        $stmt = $this->pdo->prepare("
            INSERT INTO productos 
            (uuid, marca, modelo, descripcion, precio, stock, imagen, categoria_id, created_at, updated_at)
            VALUES 
            (:uuid, :marca, :modelo, :descripcion, :precio, :stock, :imagen, :categoria_id, :created_at, :updated_at)
        ");

        return $stmt->execute([
            ':uuid' => $uuid,
            ':marca' => $producto->marca,
            ':modelo' => $producto->modelo,
            ':descripcion' => $producto->descripcion,
            ':precio' => $producto->precio,
            ':stock' => $producto->stock,
            ':imagen' => $producto->imagen,
            ':categoria_id' => $producto->categoriaId,
            ':created_at' => $now,
            ':updated_at' => $now
        ]);
    }

    // Actualiza un producto existente por su ID
    // Modifica todos los campos editables y actualiza la fecha de modificación
    // Retorna true si la operación fue exitosa
    public function update(Producto $producto)
    {
        $now = date('Y-m-d H:i:s');

        $stmt = $this->pdo->prepare("
            UPDATE productos 
            SET marca = :marca,
                modelo = :modelo,
                descripcion = :descripcion,
                precio = :precio,
                stock = :stock,
                imagen = :imagen,
                categoria_id = :categoria_id,
                updated_at = :updated_at
            WHERE id = :id
        ");

        return $stmt->execute([
            ':marca' => $producto->marca,
            ':modelo' => $producto->modelo,
            ':descripcion' => $producto->descripcion,
            ':precio' => $producto->precio,
            ':stock' => $producto->stock,
            ':imagen' => $producto->imagen,
            ':categoria_id' => $producto->categoriaId,
            ':updated_at' => $now,
            ':id' => $producto->id
        ]);
    }

    // Elimina físicamente un producto de la base de datos por su ID
    // Retorna true si la operación fue exitosa
    public function deleteById($id)
    {
        $stmt = $this->pdo->prepare("DELETE FROM productos WHERE id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }
}