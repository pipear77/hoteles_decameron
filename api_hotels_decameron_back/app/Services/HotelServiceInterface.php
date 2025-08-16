<?php

namespace App\Services;

use App\Models\Hotel;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Validation\ValidationException;

interface HotelServiceInterface
{
    /**
     * Obtiene una colección de hoteles, opcionalmente filtrada por nombre.
     *
     * @param string|null $name
     * @return Collection<int, Hotel>
     */
    public function getAll(?string $name = null): Collection;

    /**
     * Obtiene un hotel por su ID, con la relación de la ciudad cargada.
     *
     * @param int $id
     * @return Hotel|null
     */
    public function getByIdWithCity(int $id): ?Hotel;

    /**
     * Crea un hotel y sus configuraciones de habitaciones de forma atómica.
     *
     * @param array<string, mixed> $data
     * @return Hotel
     * @throws ValidationException
     */
    public function create(array $data): Hotel;

    /**
     * Actualiza un hotel y sus configuraciones de habitaciones de forma atómica.
     *
     * @param int $id
     * @param array<string, mixed> $data
     * @return Hotel|null
     * @throws ValidationException
     */
    public function update(int $id, array $data): ?Hotel;

    /**
     * Elimina un hotel por su ID.
     *
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool;

}
