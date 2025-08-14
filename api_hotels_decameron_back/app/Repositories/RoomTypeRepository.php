<?php

namespace App\Repositories;

use App\Models\RoomType;
use Illuminate\Database\Eloquent\Collection;

class RoomTypeRepository implements RoomTypeRepositoryInterface
{
    /**
     * Obtener todos los tipos de habitación.
     *
     * @return Collection<int, RoomType>
     */
    public function all(): Collection
    {
        return RoomType::all();
    }

    /**
     * Encontrar un tipo de habitación por su ID.
     *
     * @param int $id
     * @return RoomType|null
     */
    public function find(int $id): ?RoomType
    {
        return RoomType::find($id);
    }

    /**
     * Encontrar un tipo de habitación por su nombre.
     *
     * @param string $name
     * @return RoomType|null
     */
    public function findByName(string $name): ?RoomType
    {
        return RoomType::where('name', $name)->first();
    }
}
