<?php

namespace App\Repositories;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

interface UserRepositoryInterface
{
    /**
     * Obtener todos los usuarios.
     *
     * @return Collection<int, User>
     */
    public function all(): Collection;

    /**
     * Encontrar un usuario por su ID.
     *
     * @param int $id
     * @return User|null
     */
    public function find(int $id): ?User;

    /**
     * Encontrar un usuario por su email.
     *
     * @param string $email
     * @return User|null
     */
    public function findByEmail(string $email): ?User;

    /**
     * Crear un nuevo usuario.
     *
     * @param array $data
     * @return User
     */
    public function create(array $data): User;

    /**
     * Actualiza un usuario por su ID.
     *
     * @param int $id
     * @param array $data
     * @return User|null
     */
    public function update(int $id, array $data): ?User;

    /**
     * Elimina un usuario por su ID.
     *
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool;
}
