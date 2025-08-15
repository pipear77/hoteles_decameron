<?php

namespace App\Repositories;

use App\Models\RoomType;
use Illuminate\Database\Eloquent\Collection;

interface RoomTypeRepositoryInterface
{
    /**
     * Obtener todos los tipos de habitación.
     *
     * @return Collection<int, RoomType>
     */
    public function all(): Collection;

    /**
     * Crear una nuevo tipo de habitación.
     *
     * @param array $data
     * @return RoomType
     */
    public function create(array $data): RoomType;

    /**
     * Encontrar un tipo de habitación por su ID.
     *
     * @param int $id
     * @return RoomType|null
     */
    public function find(int $id): ?RoomType;

    /**
     * Actualizar un tipo de habitación.
     *
     * @param int $id
     * @param array $data
     * @return RoomType|null
     */
    public function update(int $id, array $data): ?RoomType;


    /**
     * Metodo para eliminar un tipo de habitación
     *
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool;
}
