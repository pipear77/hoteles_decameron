<?php

namespace App\Services;

interface UserServiceInterface
{
    /**
     * Autentica a un usuario con sus credenciales.
     *
     * @param array $credentials
     * @return array
     */
    public function authenticateUser(array $credentials): array;

    /**
     * Cierra la sesión de un usuario.
     *
     * @return bool
     */
    public function logoutUser(): bool;
}
