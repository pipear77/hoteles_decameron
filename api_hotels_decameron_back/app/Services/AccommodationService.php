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
        private RoomTypeRepositoryInterface $roomTypeRepository
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
     * Obtener las acomodaciones de un hotel específico.
     *
     * @param Hotel $hotel
     * @return Collection<int, Accommodation>
     */
    public function getByHotel(Hotel $hotel): Collection
    {
        return $this->repository->getByHotel($hotel);
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
    public function create(int $hotelId, array $data): Accommodation
    {
        // 1. Obtener el tipo de habitación para validar las acomodaciones permitidas.
        $roomType = $this->roomTypeRepository->find($data['room_type_id']);

        if (!$roomType) {
            throw new ValidationException('El tipo de habitación no es válido.');
        }

        // 2. Validar que la acomodación sea válida para el tipo de habitación
        // según los criterios del documento.
        $accommodationMap = [
            'Estándar' => ['Sencilla', 'Doble'],
            'Junior'   => ['Triple', 'Cuádruple'],
            'Suite'    => ['Sencilla', 'Doble', 'Triple'],
        ];

        // Se lanza una excepción si la acomodación no está permitida.
        if (!in_array($data['accommodation'], $accommodationMap[$roomType->name])) {
            throw ValidationException::withMessages([
                'accommodation' => "La acomodación '{$data['accommodation']}' no es válida para el tipo de habitación '{$roomType->name}'."
            ]);
        }

        // 3. Delegar la creación de la acomodación al repositorio.
        $data['hotel_id'] = $hotelId;
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
