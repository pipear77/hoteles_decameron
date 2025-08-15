<?php

namespace App\Services;

use App\Models\Accommodation;
use App\Models\Hotel;
use Illuminate\Database\Eloquent\Collection;

interface AccommodationServiceInterface
{
    /**
     * Obtener todas las acomodaciones.
     *
     * @return Collection<int, Accommodation>
     */
    public function getAll(): Collection;

    /**
     * Encontrar una acomodación por su ID.
     *
     * @param int $id
     * @return Accommodation|null
     */
    public function find(int $id): ?Accommodation;

    /**
     * Crear una nueva acomodación para un hotel.
     *
     * @param array $data
     * @return Accommodation
     */
    public function create(array $data): Accommodation;

    /**
     * Actualizar una acomodación existente.
     *
     * @param int $id
     * @param array $data
     * @return Accommodation|null
     */
    public function update(int $id, array $data): ?Accommodation;

    /**
     * Eliminar una acomodación.
     *
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool;
}
