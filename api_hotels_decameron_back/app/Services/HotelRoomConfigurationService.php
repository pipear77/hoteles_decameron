<?php

namespace App\Services;

use App\Models\Hotel;
use App\Models\HotelRoomConfiguration;
use App\Repositories\HotelRepositoryInterface;
use App\Repositories\HotelRoomConfigurationRepositoryInterface;
use Illuminate\Validation\ValidationException;

class HotelRoomConfigurationService implements HotelRoomConfigurationServiceInterface
{
    /**
     * @param HotelRoomConfigurationRepositoryInterface $roomConfigurationRepository
     * @param HotelRepositoryInterface $hotelRepository
     */
    public function __construct(
        private HotelRoomConfigurationRepositoryInterface $roomConfigurationRepository,
        private HotelRepositoryInterface $hotelRepository
    ) {}

    /**
     * Crea una nueva configuración de habitación para un hotel.
     *
     * @param int $hotelId
     * @param array $data
     * @return HotelRoomConfiguration
     * @throws ValidationException
     */
    public function create(int $hotelId, array $data): HotelRoomConfiguration
    {
        $hotel = $this->hotelRepository->find($hotelId);

        if (!$hotel) {
            throw ValidationException::withMessages([
                'hotel_id' => 'The specified hotel does not exist.',
            ]);
        }

        $existingRooms = $this->roomConfigurationRepository->getTotalRoomQuantityByHotelId($hotelId);
        $this->validateRoomsTotal($hotel, $existingRooms + $data['quantity'], 'create');

        return $this->roomConfigurationRepository->create($data + ['hotel_id' => $hotelId]);
    }

    /**
     * Actualiza una configuración de habitación existente.
     *
     * @param int $id
     * @param array $data
     * @return HotelRoomConfiguration|null
     * @throws ValidationException
     */
    public function update(int $id, array $data): ?HotelRoomConfiguration
    {
        $configuration = $this->roomConfigurationRepository->find($id);

        if (!$configuration) {
            return null;
        }

        $hotel = $this->hotelRepository->find($configuration->hotel_id);

        if (!$hotel) {
            throw ValidationException::withMessages([
                'hotel_id' => 'The specified hotel does not exist.',
            ]);
        }

        // Se excluye la cantidad de la configuración actual
        $existingRooms = $this->roomConfigurationRepository->getTotalRoomQuantityByHotelId($hotel->id);
        $newTotal = $existingRooms - $configuration->quantity + $data['quantity'];

        $this->validateRoomsTotal($hotel, $newTotal, 'update');

        $this->roomConfigurationRepository->update($id, $data);

        return $this->roomConfigurationRepository->find($id);
    }

    /**
     * Elimina una configuración de habitación por su ID.
     *
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool
    {
        return $this->roomConfigurationRepository->delete($id);
    }

    /**
     * Valida que la cantidad total de habitaciones no exceda la capacidad del hotel.
     *
     * @param Hotel $hotel
     * @param int $newTotal
     * @param string $operation
     * @throws ValidationException
     */
    private function validateRoomsTotal(Hotel $hotel, int $newTotal, string $operation): void
    {
        if ($newTotal > $hotel->rooms_total) {
            $message = $operation === 'create'
                ? 'The total quantity of rooms exceeds the hotel\'s capacity of ' . $hotel->rooms_total . '.'
                : 'The updated quantity of rooms exceeds the hotel\'s capacity of ' . $hotel->rooms_total . '.';

            throw ValidationException::withMessages(['quantity' => $message]);
        }
    }
}
