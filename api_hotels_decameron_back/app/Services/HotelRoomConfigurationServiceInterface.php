<?php

namespace App\Services;

use App\Models\HotelRoomConfiguration;
use Illuminate\Validation\ValidationException;

interface HotelRoomConfigurationServiceInterface
{
    /**
     * Crea una nueva configuración de habitación para un hotel.
     *
     * @param int $hotelId
     * @param array $data
     * @return HotelRoomConfiguration
     * @throws ValidationException
     */
    public function create(int $hotelId, array $data): HotelRoomConfiguration;

    /**
     * Actualiza una configuración de habitación existente.
     *
     * @param int $id
     * @param array $data
     * @return HotelRoomConfiguration
     * @throws ValidationException
     */
    public function update(int $id, array $data): ?HotelRoomConfiguration;

    /**
     * Elimina una configuración de habitación por su ID.
     *
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool;
}
