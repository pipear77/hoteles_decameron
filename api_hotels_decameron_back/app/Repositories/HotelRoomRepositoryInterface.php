<?php

namespace App\Repositories;

use App\Models\HotelRoom;
use Illuminate\Database\Eloquent\Collection;

interface HotelRoomRepositoryInterface
{
    /**
     * Obtener todas las habitaciones de hotel.
     *
     * @return Collection<int, HotelRoom>
     */
    public function all(): Collection;

    /**
     * Encontrar una habitación de hotel por su ID.
     *
     * @param int $id
     * @return HotelRoom|null
     */
    public function find(int $id): ?HotelRoom;

    /**
     * Obtener las habitaciones de un hotel específico.
     *
     * @param int $hotelId
     * @return Collection<int, HotelRoom>
     */
    public function getByHotelId(int $hotelId): Collection;

    /**
     * Contar las habitaciones de un hotel específico.
     *
     * @param int $hotelId
     * @return int
     */
    public function countByHotelId(int $hotelId): int;

    /**
     * Crear una nueva habitación de hotel.
     *
     * @param array $data
     * @return HotelRoom
     */
    public function create(array $data): HotelRoom;
}
