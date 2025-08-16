<?php

namespace App\Services;

use App\Models\Hotel;
use App\Repositories\HotelRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class HotelService implements HotelServiceInterface
{
    public function __construct(private HotelRepositoryInterface $repository) {}

    /**
     * Obtener todos los hoteles, con un filtro opcional por nombre.
     *
     * @param string|null $name
     * @return Collection<int, Hotel>
     */
    public function getAll(?string $name = null): Collection
    {
        if ($name) {
            return $this->repository->findByName($name);
        }
        return $this->repository->all();
    }

    /**
     * Obtener un hotel por su ID.
     *
     * @param int $id
     * @return Hotel|null
     */
    public function getById(int $id): ?Hotel
    {
        return $this->repository->find($id);
    }

    /**
     * Crear un nuevo hotel.
     *
     * @param array<string, mixed> $data
     * @return Hotel
     */
    public function create(array $data): Hotel
    {
        return $this->repository->create($data);
    }

    /**
     * Actualizar un hotel existente.
     *
     * @param int $id
     * @param array<string, mixed> $data
     * @return Hotel|null
     */
    public function update(int $id, array $data): ?Hotel
    {
        return $this->repository->update($id, $data);
    }

    /**
     * Eliminar un hotel existente.
     *
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool
    {
        return $this->repository->delete($id);
    }
}
