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
     * Encontrar un tipo de habitación por su nombre.
     *
     * @param string $name
     * @return RoomType|null
     */
    public function findByName(string $name): ?RoomType
    {
        return $this->repository->findByName($name);
    }
}
