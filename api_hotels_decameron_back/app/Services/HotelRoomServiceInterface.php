<?php

namespace App\Services;

use App\Models\HotelRoom;
use App\Models\Hotel;
use Illuminate\Database\Eloquent\Collection;

interface HotelRoomServiceInterface
{
    /**
     * Obtener todas las habitaciones de hotel.
     *
     * @return Collection<int, HotelRoom>
     */
    public function getAll(): Collection;

    /**
     * Obtener las habitaciones de un hotel específico.
     *
     * @param int $hotelId
     * @return Collection<int, HotelRoom>
     */
    public function getByHotelId(int $hotelId): Collection;

    /**
     * Encontrar una habitación por su ID.
     *
     * @param int $id
     * @return HotelRoom|null
     */
    public function find(int $id): ?HotelRoom;

    /**
     * Crear una nueva habitación para un hotel.
     *
     * @param int $hotelId
     * @param array $data
     * @return HotelRoom
     */
    public function create(int $hotelId, array $data): HotelRoom;
}
