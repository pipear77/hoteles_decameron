<?php

namespace App\Repositories;

use App\Models\HotelRoomConfiguration;
use Illuminate\Database\Eloquent\Collection;

interface HotelRoomConfigurationRepositoryInterface
{
    /**
     * Obtener todas las habitaciones de hotel.
     *
     * @return Collection<int, HotelRoomConfiguration>
     */
    public function all(): Collection;

    /**
     * Encontrar una habitación de hotel por su ID.
     *
     * @param int $id
     * @return HotelRoomConfiguration|null
     */
    public function find(int $id): ?HotelRoomConfiguration;

    /**
     * Obtener las habitaciones de un hotel específico.
     *
     * @param int $hotelId
     * @return Collection<int, HotelRoomConfiguration>
     */
    public function getByHotelId(int $hotelId): Collection;

    /**
     * Contar las habitaciones de un hotel específico.
     *
     * @param int $hotelId
     * @return int
     */
    public function getTotalRoomQuantityByHotelId(int $hotelId): int;

    /**
     * Crear una nueva habitación de hotel.
     *
     * @param array $data
     * @return HotelRoomConfiguration
     */
    public function create(array $data): HotelRoomConfiguration;

    /**
     * Actualizar una configuración de habitación existente.
     *
     * @param int $id
     * @param array<string, mixed> $data
     * @return HotelRoomConfiguration|null
     */
    public function update(int $id, array $data): ?HotelRoomConfiguration;

    /**
     * Eliminar una configuración de habitación existente.
     *
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool;
}
