<?php

namespace App\Repositories;

use App\Models\HotelRoomConfiguration;
use Illuminate\Database\Eloquent\Collection;

interface HotelRoomConfigurationRepositoryInterface
{
    /**
     * Obtiene todas las configuraciones de habitaciones.
     *
     * @return Collection<int, HotelRoomConfiguration>
     */
    public function all(): Collection;

    /**
     * Crea una nueva configuración de habitación.
     *
     * @param array $data
     * @return HotelRoomConfiguration
     */
    public function create(array $data): HotelRoomConfiguration;

    /**
     * Busca una configuración de habitación por su ID.
     *
     * @param int $id
     * @return HotelRoomConfiguration|null
     */
    public function find(int $id): ?HotelRoomConfiguration;

    /**
     * Actualiza una configuración de habitación existente.
     *
     * @param int $id
     * @param array $data
     * @return HotelRoomConfiguration|null
     */
    public function update(int $id, array $data): ?HotelRoomConfiguration;

    /**
     * Elimina una configuración de habitación.
     *
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool;

    /**
     * Retorna las configuraciones de habitación para un hotel específico.
     *
     * @param int $hotelId
     * @return Collection<int, HotelRoomConfiguration>
     */
    public function getByHotelId(int $hotelId): Collection;

    /**
     * Retorna la cantidad total de habitaciones para un hotel específico.
     *
     * @param int $hotelId
     * @return int
     */
    public function getTotalRoomQuantityByHotelId(int $hotelId): int;

    /**
     * Elimina todas las configuraciones de habitaciones de un hotel.
     *
     * @param int $hotelId
     * @return int
     */
    public function deleteByHotelId(int $hotelId): int;

    /**
     * Calcula la cantidad total de habitaciones configuradas para un hotel,
     * excluyendo una configuración específica si se proporciona un ID.
     *
     * @param int $hotelId
     * @param int|null $excludeId
     * @return int
     */
    public function getExistingRoomsQuantity(int $hotelId, ?int $excludeId = null): int;
}
