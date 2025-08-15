<?php

namespace App\Repositories;

use App\Models\HotelRoomConfiguration;
use Illuminate\Database\Eloquent\Collection;

class HotelRoomConfigurationRepository implements HotelRoomConfigurationRepositoryInterface
{
    /**
     * Obtener todas las habitaciones de hotel.
     *
     * @return Collection<int, HotelRoomConfiguration>
     */
    public function all(): Collection
    {
        return HotelRoomConfiguration::all();
    }

    /**
     * Encontrar una habitación de hotel por su ID.
     *
     * @param int $id
     * @return HotelRoomConfiguration|null
     */
    public function find(int $id): ?HotelRoomConfiguration
    {
        return HotelRoomConfiguration::find($id);
    }

    /**
     * Obtener las habitaciones de un hotel específico.
     *
     * @param int $hotelId
     * @return Collection<int, HotelRoomConfiguration>
     */
    public function getByHotelId(int $hotelId): Collection
    {
        return HotelRoomConfiguration::where('hotel_id', $hotelId)->get();
    }

    /**
     * Contar las habitaciones de un hotel específico.
     *
     * @param int $hotelId
     * @return int
     */
    public function countByHotelId(int $hotelId): int
    {
        return HotelRoomConfiguration::where('hotel_id', $hotelId)->sum('quantity');
    }

    /**
     * Crear una nueva habitación de hotel.
     *
     * @param array $data
     * @return HotelRoomConfiguration
     */
    public function create(array $data): HotelRoomConfiguration
    {
        return HotelRoomConfiguration::create($data);
    }

    /**
     * Actualizar una configuración de habitación existente.
     *
     * @param int $id
     * @param array<string, mixed> $data
     * @return HotelRoomConfiguration|null
     */
    public function update(int $id, array $data): ?HotelRoomConfiguration
    {
        $hotelConfiguration = $this->find($id);
        if ($hotelConfiguration) {
            $hotelConfiguration->update($data);
        }
        return $hotelConfiguration;
    }

    /**
     * Eliminar una configuración de habitación existente.
     *
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool
    {
        return HotelRoomConfiguration::destroy($id) > 0;
    }
}
