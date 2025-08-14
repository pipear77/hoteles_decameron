<?php

namespace App\Services;

use App\Models\Role;
use App\Repositories\RoleRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class RoleService implements RoleServiceInterface
{
    public function __construct(private RoleRepositoryInterface $repository) {}

    /**
     * Obtener todos los roles disponibles.
     *
     * @return Collection<int, Role>
     */
    public function getAll(): Collection
    {
        return $this->repository->all();
    }

    /**
     * Encontrar un rol por su ID.
     *
     * @param int $id
     * @return Role|null
     */
    public function find(int $id): ?Role
    {
        return $this->repository->find($id);
    }

    /**
     * Encontrar un rol por su nombre.
     *
     * @param string $name
     * @return Role|null
     */
    public function findByName(string $name): ?Role
    {
        return $this->repository->findByName($name);
    }
}
