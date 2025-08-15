<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

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

    /**
     * Elimina un usuario por su ID.
     *
     * @param int $id
     * @return bool
     */
    public function deleteUser(int $id): bool;

    /**
     * Asigna un nuevo rol a un usuario, con validación de lógica de negocio.
     *
     * @param int $userId El ID del usuario a modificar.
     * @param int $newRoleId El ID del nuevo rol.
     * @return User|null
     * @throws NotFoundHttpException Si el usuario no existe.
     * @throws AccessDeniedHttpException Si el usuario intenta cambiar su propio rol o si no tiene permisos.
     */
    public function updateUserRole(int $userId, int $newRoleId): ?User;
}
