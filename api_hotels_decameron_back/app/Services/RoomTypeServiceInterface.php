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
     * Encontrar un tipo de habitación por su nombre.
     *
     * @param string $name
     * @return RoomType|null
     */
    public function findByName(string $name): ?RoomType;
}
