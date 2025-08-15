<?php

namespace App\Services;

use App\Models\RoomType;
use Illuminate\Database\Eloquent\Collection;

interface RoomTypeServiceInterface
{
    /**
     * Obtener todos los tipos de habitación.
     *
     * @return Collection<int, RoomType>
     */
    public function getAll(): Collection;

    /**
     * Encontrar un tipo de habitación por su ID.
     *
     * @param int $id
     * @return RoomType|null
     */
    public function find(int $id): ?RoomType;

    /**
     * Crear un nuevo tipo de habitación para un hotel.
     *
     * @param array $data
     * @return RoomType
     */
    public function create(array $data): RoomType;

    /**
     * Actualizar un tipo de habitación existente.
     *
     * @param int $id
     * @param array $data
     * @return RoomType|null
     */
    public function update(int $id, array $data): ?RoomType;

    /**
     * Eliminar un tipo de habitación.
     *
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool;
}
