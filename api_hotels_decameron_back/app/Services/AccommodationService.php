<?php

namespace App\Services;

use App\Models\Accommodation;
use App\Models\Hotel;
use App\Repositories\AccommodationRepositoryInterface;
use App\Repositories\RoomTypeRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Validation\ValidationException;

class AccommodationService implements AccommodationServiceInterface
{
    public function __construct(
        private AccommodationRepositoryInterface $repository,
    ) {}

    /**
     * Obtener todas las acomodaciones.
     *
     * @return Collection<int, Accommodation>
     */
    public function getAll(): Collection
    {
        return $this->repository->all();
    }

    /**
     * Encontrar una acomodación por su ID.
     *
     * @param int $id
     * @return Accommodation|null
     */
    public function find(int $id): ?Accommodation
    {
        return $this->repository->find($id);
    }

    /**
     * Crear una nueva acomodación para un hotel.
     *
     * @param int $hotelId
     * @param array $data
     * @return Accommodation
     */
    public function create(array $data): Accommodation
    {
        return $this->repository->create($data);
    }

    /**
     * Actualizar una acomodación existente.
     *
     * @param int $id
     * @param array $data
     * @return Accommodation|null
     */
    public function update(int $id, array $data): ?Accommodation
    {
        return $this->repository->update($id, $data);
    }

    /**
     * Eliminar una acomodación.
     *
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool
    {
        return $this->repository->delete($id);
    }
}
