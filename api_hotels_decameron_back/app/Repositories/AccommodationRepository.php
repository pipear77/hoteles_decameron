<?php

namespace App\Repositories;

use App\Models\Accommodation;
use Illuminate\Database\Eloquent\Collection;
use App\Models\Hotel;

class AccommodationRepository implements AccommodationRepositoryInterface
{
    /**
     * Obtener todas las acomodaciones.
     *
     * @return Collection<int, Accommodation>
     */
    public function all(): Collection
    {
        return Accommodation::all();
    }

    /**
     * Encontrar una acomodación por su ID.
     *
     * @param int $id
     * @return Accommodation|null
     */
    public function find(int $id): ?Accommodation
    {
        return Accommodation::find($id);
    }

    /**
     * Crear una nueva acomodación.
     *
     * @param array $data
     * @return Accommodation
     */
    public function create(array $data): Accommodation
    {
        return Accommodation::create($data);
    }

    /**
     * Actualizar una acomodación.
     *
     * @param int $id
     * @param array $data
     * @return Accommodation|null
     */
    public function update(int $id, array $data): ?Accommodation
    {
        $accommodation = $this->find($id);
        if ($accommodation) {
            $accommodation->update($data);
        }
        return $accommodation;
    }

    /**
     * Eliminar una acomodación.
     *
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool
    {
        return Accommodation::destroy($id) > 0;
    }
}
