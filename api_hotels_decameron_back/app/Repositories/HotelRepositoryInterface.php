<?php

namespace App\Repositories;

use App\Models\Hotel;
use Illuminate\Database\Eloquent\Collection;

interface HotelRepositoryInterface
{
    /**
     * Obtener todos los hoteles.
     *
     * @return Collection<int, Hotel>
     */
    public function all(): Collection;

    /**
     * Encontrar un hotel por su ID.
     *
     * @param int $id
     * @return Hotel|null
     */
    public function find(int $id): ?Hotel;

    /**
     * Encontrar un hotel por su nombre.
     *
     * @param string $name
     * @return Collection<int, Hotel>
    */
    public function findByName(string $name): Collection;


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
     * Eliminar un hotel.
     *
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool;
}
