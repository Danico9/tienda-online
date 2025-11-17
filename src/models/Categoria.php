<?php
/**
 * Modelo de datos para la entidad Categoria
 *
 * Representa una categoría de productos en la tienda online.
 * Sirve como estructura de datos entre la base de datos y las capas de servicio/presentación.
 */
namespace models;

class Categoria
{
    // Identificador único de la categoría (UUID)
    public $id;

    // Nombre de la categoría (ej: "DEPORTES", "COMIDA")
    public $nombre;

    // Fecha de creación del registro
    public $createdAt;

    // Fecha de última actualización
    public $updatedAt;

    // Marca de borrado lógico (false = activa, true = eliminada)
    public $isDeleted;

    // Constructor: inicializa la categoría con valores opcionales
    public function __construct(
        $id = null,
        $nombre = null,
        $createdAt = null,
        $updatedAt = null,
        $isDeleted = false
    )
    {
        $this->id = $id;
        $this->nombre = $nombre;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
        $this->isDeleted = $isDeleted;
    }
}