<?php

namespace App\Services;

use App\Models\Hotel;
use App\Models\HotelRoomConfiguration;
use App\Repositories\HotelRoomConfigurationRepositoryInterface;
use App\Repositories\HotelRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Validation\ValidationException;
class HotelRoomConfigurationService implements HotelRoomConfigurationServiceInterface
{
    public function __construct(
        private HotelRoomConfigurationRepositoryInterface $repository,
        private HotelRepositoryInterface $hotelRepository
    ) {}

    /**
     * Obtener todas las configuraciones de habitaciones.
     *
     * @return Collection<int, HotelRoomConfiguration>
     */
    public function getAll(): Collection
    {
        return $this->repository->all();
    }

    /**
     * Obtener las configuraciones de un hotel específico.
     *
     * @param int $hotelId
     * @return Collection<int, HotelRoomConfiguration>
     */
    public function getByHotelId(int $hotelId): Collection
    {
        return $this->repository->getByHotelId($hotelId);
    }

    /**
     * Encontrar una configuración de habitación por su ID.
     *
     * @param int $id
     * @return HotelRoomConfiguration|null
     */
    public function find(int $id): ?HotelRoomConfiguration
    {
        return $this->repository->find($id);
    }

    /**
     * Crear una nueva configuración para un hotel.
     *
     * @param int $hotelId
     * @param array<string, mixed> $data
     * @return HotelRoomConfiguration
     * @throws ValidationException
     */
    public function create(int $hotelId, array $data): HotelRoomConfiguration
    {
        $hotel = $this->hotelRepository->find($hotelId);
        if (!$hotel) {
            throw ValidationException::withMessages(['hotel_id' => 'El hotel especificado no existe.']);
        }
        $data['hotel_id'] = $hotelId;

        $this->validateTotalRooms($hotel, $data['quantity']);

        return $this->repository->create($data);
    }

    /**
     * Actualizar una configuración de habitación existente.
     *
     * @param int $id
     * @param array<string, mixed> $data
     * @return HotelRoomConfiguration|null
     * @throws ValidationException
     */
    public function update(int $id, array $data): ?HotelRoomConfiguration
    {
        $hotelConfiguration = $this->repository->find($id);
        if (!$hotelConfiguration) {
            return null;
        }

        $hotel = $this->hotelRepository->find($hotelConfiguration->hotel_id);

        $quantityToAdd = $data['quantity'] ?? $hotelConfiguration->quantity;
        $quantityToRemove = $hotelConfiguration->quantity;

        // Validamos la nueva cantidad total de habitaciones.
        $this->validateTotalRooms($hotel, $quantityToAdd, $quantityToRemove);

        return $this->repository->update($id, $data);
    }

    /**
     * Eliminar una configuración de habitación.
     *
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool
    {
        return $this->repository->delete($id);
    }

    /**
     * Valida que la cantidad total de habitaciones no supere el máximo del hotel.
     *
     * @param Hotel $hotel
     * @param int $quantityToAdd
     * @param int $quantityToRemove
     * @return void
     * @throws ValidationException
     */
    private function validateTotalRooms(Hotel $hotel, int $quantityToAdd, int $quantityToRemove = 0): void
    {
        // Se calcula la suma actual de habitaciones del hotel (excluyendo la que se va a eliminar).
        $existingRooms = $this->repository->getTotalRoomQuantityByHotelId($hotel->id);
        $totalRooms = $existingRooms - $quantityToRemove + $quantityToAdd;

        if ($totalRooms > $hotel->rooms_total) {
            throw ValidationException::withMessages([
                'quantity' => "La cantidad de habitaciones supera el máximo ({$hotel->rooms_total}) para este hotel."
            ]);
        }
    }
}
