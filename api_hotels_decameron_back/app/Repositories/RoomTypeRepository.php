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
     * Crear una nueva acomodación.
     *
     * @param array $data
     * @return RoomType
     */
    public function create(array $data): RoomType
    {
        // TODO: Implement create() method.
        return RoomType::create($data);
    }

    /**
     * Actualizar un tipo de habitación.
     *
     * @param int $id
     * @param array $data
     * @return RoomType|null
     */
    public function update(int $id, array $data): ?RoomType
    {
        // TODO: Implement update() method.
        $roomType = $this->find($id);
        if($roomType)
        {
            $roomType->update($data);
        }
        return $roomType;
    }

    /**
     * Metodo para eliminar un tipo de habitación
     *
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool
    {
        // TODO: Implement delete() method.
        return RoomType::destroy($id) > 0;

    }
}
