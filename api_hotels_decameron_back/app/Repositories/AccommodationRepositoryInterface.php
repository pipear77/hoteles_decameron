<?php

namespace App\Repositories;
use App\Models\Accommodation;
use App\Models\Hotel;
use Illuminate\Database\Eloquent\Collection;

interface AccommodationRepositoryInterface
{
    /**
     * Obtener todas las acomodaciones.
     *
     * @return Collection<int, Accommodation>
     */
    public function all(): Collection;

    /**
     * Encontrar una acomodación por su ID.
     *
     * @param int $id
     * @return Accommodation|null
     */
    public function find(int $id): ?Accommodation;

}
