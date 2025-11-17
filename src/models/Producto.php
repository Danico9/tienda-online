<?php
/**
 * Modelo de datos para la entidad Producto
 *
 * Representa un producto del catálogo de la tienda online.
 * Encapsula todos los atributos necesarios para la gestión del CRUD de productos.
 * Relación N:1 con Categoria.
 */
namespace models;

class Producto
{
    // Identificador numérico único (autoincremental)
    public $id;

    // Identificador universal único (UUID)
    public $uuid;

    // Marca del producto (ej: "Nike", "Adidas")
    public $marca;

    // Modelo específico del producto
    public $modelo;

    // Descripción detallada del producto
    public $descripcion;

    // Precio en euros
    public $precio;

    // Cantidad disponible en inventario
    public $stock;

    // URL de la imagen del producto
    public $imagen;

    // ID de la categoría asociada (Foreign Key)
    public $categoriaId;

    // Nombre de la categoría (obtenido mediante JOIN, no almacenado en BD)
    public $categoriaNombre;

    // Fecha de creación del producto
    public $createdAt;

    // Fecha de última modificación
    public $updatedAt;

    // Marca de borrado lógico (false = activo, true = eliminado)
    public $isDeleted;

    // URL de imagen por defecto cuando no se proporciona una personalizada
    private const IMAGEN_DEFAULT = 'https://via.placeholder.com/150';

    // Constructor: inicializa el producto con todos sus atributos
    // Si no se proporciona imagen, asigna automáticamente la imagen por defecto
    public function __construct(
        $id = null,
        $uuid = null,
        $marca = null,
        $modelo = null,
        $descripcion = null,
        $precio = 0.0,
        $stock = 0,
        $imagen = null,
        $categoriaId = null,
        $categoriaNombre = null,
        $createdAt = null,
        $updatedAt = null,
        $isDeleted = false
    )
    {
        $this->id = $id;
        $this->uuid = $uuid;
        $this->marca = $marca;
        $this->modelo = $modelo;
        $this->descripcion = $descripcion;
        $this->precio = $precio;
        $this->stock = $stock;
        // Asigna imagen por defecto si no se proporciona una
        $this->imagen = $imagen ?? self::IMAGEN_DEFAULT;
        $this->categoriaId = $categoriaId;
        $this->categoriaNombre = $categoriaNombre;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
        $this->isDeleted = $isDeleted;
    }
}