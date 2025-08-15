<?php

namespace App\Services;

use App\Models\RoomType;
use App\Repositories\RoomTypeRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class RoomTypeService implements RoomTypeServiceInterface
{
    public function __construct(private RoomTypeRepositoryInterface $repository) {}

    /**
     * Obtener todos los tipos de habitación.
     *
     * @return Collection<int, RoomType>
     */
    public function getAll(): Collection
    {
        return $this->repository->all();
    }

    /**
     * Encontrar un tipo de habitación por su ID.
     *
     * @param int $id
     * @return RoomType|null
     */
    public function find(int $id): ?RoomType
    {
        return $this->repository->find($id);
    }

    /**
     * Crear un nuevo tipo de habitación para un hotel.
     *
     * @param int $hotelId
     * @param array $data
     * @return RoomType
     */
    public function create(array $data): RoomType
    {
        return $this->repository->create($data);
    }

    /**
     * Actualizar un tipo de habitación existente.
     *
     * @param int $id
     * @param array $data
     * @return RoomType|null
     */
    public function update(int $id, array $data): ?RoomType
    {
        return $this->repository->update($id, $data);
    }

    /**
     * Eliminar un tipo de habitación.
     *
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool
    {
        return $this->repository->delete($id);
    }
}
