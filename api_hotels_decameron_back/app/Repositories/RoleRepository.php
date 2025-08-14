<?php

namespace App\Repositories;

use App\Models\Role;
use Illuminate\Database\Eloquent\Collection;

class RoleRepository implements RoleRepositoryInterface
{
    /**
     * Obtener todos los roles.
     *
     * @return Collection<int, Role>
     */
    public function all(): Collection
    {
        return Role::all();
    }

    /**
     * Encontrar un rol por su ID.
     *
     * @param int $id
     * @return Role|null
     */
    public function find(int $id): ?Role
    {
        return Role::find($id);
    }

    /**
     * Encontrar un rol por su nombre.
     *
     * @param string $name
     * @return Role|null
     */
    public function findByName(string $name): ?Role
    {
        return Role::where('name', $name)->first();
    }
}
