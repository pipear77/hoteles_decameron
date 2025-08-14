<?php

namespace App\Repositories;

use App\Models\HotelRoom;
use Illuminate\Database\Eloquent\Collection;

class HotelRoomRepository implements HotelRoomRepositoryInterface
{
    /**
     * Obtener todas las habitaciones de hotel.
     *
     * @return Collection<int, HotelRoom>
     */
    public function all(): Collection
    {
        return HotelRoom::all();
    }

    /**
     * Encontrar una habitación de hotel por su ID.
     *
     * @param int $id
     * @return HotelRoom|null
     */
    public function find(int $id): ?HotelRoom
    {
        return HotelRoom::find($id);
    }

    /**
     * Obtener las habitaciones de un hotel específico.
     *
     * @param int $hotelId
     * @return Collection<int, HotelRoom>
     */
    public function getByHotelId(int $hotelId): Collection
    {
        return HotelRoom::where('hotel_id', $hotelId)->get();
    }

    /**
     * Contar las habitaciones de un hotel específico.
     *
     * @param int $hotelId
     * @return int
     */
    public function countByHotelId(int $hotelId): int
    {
        return HotelRoom::where('hotel_id', $hotelId)->sum('quantity');
    }

    /**
     * Crear una nueva habitación de hotel.
     *
     * @param array $data
     * @return HotelRoom
     */
    public function create(array $data): HotelRoom
    {
        return HotelRoom::create($data);
    }
}
