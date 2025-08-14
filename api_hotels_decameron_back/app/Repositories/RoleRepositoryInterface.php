<?php

namespace App\Repositories;

use App\Models\Role;
use Illuminate\Database\Eloquent\Collection;

interface RoleRepositoryInterface
{
    /**
     * Obtener todos los roles.
     *
     * @return Collection<int, Role>
     */
    public function all(): Collection;

    /**
     * Encontrar un rol por su ID.
     *
     * @param int $id
     * @return Role|null
     */
    public function find(int $id): ?Role;

    /**
     * Encontrar un rol por su nombre.
     *
     * @param string $name
     * @return Role|null
     */
    public function findByName(string $name): ?Role;
}
