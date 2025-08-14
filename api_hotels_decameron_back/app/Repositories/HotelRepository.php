<?php

namespace App\Repositories;

use App\Models\Hotel;
use Illuminate\Database\Eloquent\Collection;

class HotelRepository implements HotelRepositoryInterface
{
    /**
     * Obtener todos los hoteles.
     *
     * @return Collection<int, Hotel>
     */
    public function all(): Collection
    {
        return Hotel::all();
    }

    /**
     * Encontrar un hotel por su ID.
     *
     * @param int $id
     * @return Hotel|null
     */
    public function find(int $id): ?Hotel
    {
        return Hotel::find($id);
    }

    /**
     * Crear un nuevo hotel.
     *
     * @param array $data
     * @return Hotel
     */
    public function create(array $data): Hotel
    {
        return Hotel::create($data);
    }

    /**
     * Actualizar un hotel existente.
     *
     * @param int $id
     * @param array $data
     * @return Hotel|null
     */
    public function update(int $id, array $data): ?Hotel
    {
        $hotel = $this->find($id);
        if ($hotel) {
            $hotel->update($data);
        }
        return $hotel;
    }

    /**
     * Eliminar un hotel.
     *
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool
    {
        return Hotel::destroy($id) > 0;
    }
}
