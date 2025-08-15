<?php

namespace App\Services;

use App\Models\HotelRoomConfiguration;
use App\Models\Hotel;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Validation\ValidationException;

interface HotelRoomConfigurationServiceInterface
{
    /**
     * Obtener todas las habitaciones de hotel.
     *
     * @return Collection<int, HotelRoomConfiguration>
     */
    public function getAll(): Collection;

    /**
     * Obtener las habitaciones de un hotel específico.
     *
     * @param int $hotelId
     * @return Collection<int, HotelRoomConfiguration>
     */
    public function getByHotelId(int $hotelId): Collection;

    /**
     * Encontrar una habitación por su ID.
     *
     * @param int $id
     * @return HotelRoomConfiguration|null
     */
    public function find(int $id): ?HotelRoomConfiguration;

    /**
     * Crear una nueva habitación para un hotel.
     *
     * @param int $hotelId
     * @param array $data
     * @return HotelRoomConfiguration
     */
    public function create(int $hotelId, array $data): HotelRoomConfiguration;

    /**
     * Actualizar una configuración de habitación existente.
     *
     * @param int $id
     * @param array $data
     * @return HotelRoomConfiguration|null
     * @throws ValidationException
     */
    public function update(int $id, array $data): ?HotelRoomConfiguration;

    /**
     * Eliminar una configuración de habitación.
     *
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool;

}
