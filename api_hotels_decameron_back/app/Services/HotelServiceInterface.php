<?php

namespace App\Services;

use App\Models\Hotel;
use Illuminate\Database\Eloquent\Collection;

interface HotelServiceInterface
{
    /**
     * Obtener todos los hoteles.
     *
     * @return Collection<int, Hotel>
     */
    public function getAll(?string $name = null): Collection;

    /**
     * Encontrar un hotel por su id.
     *
     * @param int $id
     * @return Hotel|null
     */
    public function getById(int $id): ?Hotel;

    /**
     * Crear un nuevo hotel.
     *
     * @param array $data
     * @return Hotel
     */
    public function create(array $data): Hotel;

    /**
     * Actualizar un hotel existente.
     *
     * @param int $id
     * @param array $data
     * @return Hotel|null
     */
    public function update(int $id, array $data): ?Hotel;

    /**
     * Borrar un hotel por su ID.
     *
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool;
}
