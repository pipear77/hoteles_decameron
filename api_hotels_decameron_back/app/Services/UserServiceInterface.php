<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Validation\ValidationException;

interface UserServiceInterface
{
    /**
     * Obtener todos los usuarios.
     *
     * @return Collection<int, User>
     */
    public function getAllUsers(): Collection;

    /**
     * Registrar un nuevo usuario.
     *
     * @param array $userData
     * @return User
     */
    public function registerUser(array $userData): User;

    /**
     * Autenticar un usuario con email y contraseña.
     *
     * @param array $credentials
     * @return array
     * @throws ValidationException
     */
    public function authenticateUser(array $credentials): array;


    /**
     * Asigna roles a un usuario.
     *
     * @param int $userId
     * @param array $roleIds
     * @return User|null
     */
    public function assignRoles(int $userId, array $roleIds): ?User;
    /**
     * Busca un usuario por su ID.
     *
     * @param int $id
     * @return User|null
     */
    public function findUserById(int $id): ?User;

    /**
     * Actualiza un usuario por su ID.
     *
     * @param int $id
     * @param array $userData
     * @return User|null
     */
    public function updateUser(int $id, array $userData): ?User;
}
