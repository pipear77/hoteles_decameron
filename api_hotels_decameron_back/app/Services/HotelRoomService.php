<?php

namespace App\Services;

use App\Models\HotelRoom;
use App\Repositories\HotelRoomRepositoryInterface;
use App\Repositories\HotelRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Validation\ValidationException;
class HotelRoomService implements HotelRoomServiceInterface
{
    public function __construct(
        private HotelRoomRepositoryInterface $repository,
        private HotelRepositoryInterface $hotelRepository
    ) {}

    /**
     * Obtener todas las habitaciones de hotel.
     *
     * @return Collection<int, HotelRoom>
     */
    public function getAll(): Collection
    {
        return $this->repository->all();
    }

    /**
     * Obtener las habitaciones de un hotel específico.
     *
     * @param int $hotelId
     * @return Collection<int, HotelRoom>
     */
    public function getByHotelId(int $hotelId): Collection
    {
        return $this->repository->getByHotelId($hotelId);
    }

    /**
     * Encontrar una habitación por su ID.
     *
     * @param int $id
     * @return HotelRoom|null
     */
    public function find(int $id): ?HotelRoom
    {
        return $this->repository->find($id);
    }

    /**
     * Crear una nueva habitación para un hotel.
     *
     * @param int $hotelId
     * @param array $data
     * @return HotelRoom
     */
    public function create(int $hotelId, array $data): HotelRoom
    {
        $hotel = $this->hotelRepository->find($hotelId);

        if (!$hotel) {
            throw new ValidationException('El hotel especificado no existe.');
        }

        // 1. Lógica de negocio: La cantidad de habitaciones configuradas no debe
        // superar el máximo por hotel.
        $existingRooms = $this->repository->countByHotelId($hotelId);
        if ($existingRooms + $data['quantity'] > $hotel->rooms_total) {
            throw ValidationException::withMessages([
                'quantity' => "La cantidad de habitaciones supera el máximo ({$hotel->rooms_total}) para este hotel."
            ]);
        }

        // 2. Delegar la creación de la habitación al repositorio.
        $data['hotel_id'] = $hotelId;
        return $this->repository->create($data);
    }
}
