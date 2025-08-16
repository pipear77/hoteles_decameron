<?php

namespace App\Repositories;

use App\Models\Accommodation;
use Illuminate\Database\Eloquent\Collection;

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
}
