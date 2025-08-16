<?php

namespace App\Services;


use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class UserService implements UserServiceInterface
{

    /**
     * Autentica a un usuario con sus credenciales.
     *
     * @param array $credentials
     * @return array
     */
    public function authenticateUser(array $credentials): array
    {
        // Se intenta la autenticación con las credenciales proporcionadas.
        if (!Auth::attempt($credentials)) {
            // Si la autenticación falla, lanzamos una excepción de validación.
            throw ValidationException::withMessages([
                'email' => 'Las credenciales proporcionadas son incorrectas.'
            ]);
        }

        // Si la autenticación es exitosa, obtenemos el usuario autenticado.
        $user = Auth::user();

        // Creamos un token de acceso para el usuario.
        $token = $user->createToken('auth-token')->plainTextToken;

        // Retornamos un array con el estado, mensaje, token y datos del usuario.
        return [
            'status'  => true,
            'message' => '¡Autenticación exitosa!',
            'token'   => $token,
            'user'    => [
                'id'    => $user->id,
                'email' => $user->email,
            ],
        ];
    }

    /**
     * Cierra la sesión de un usuario.
     *
     * @return bool
     */
    public function logoutUser(): bool
    {
        // Se obtiene el token actual y se elimina.
        $deleted = Auth::user()->currentAccessToken()->delete();

        // Se retorna true si el token fue eliminado, false en caso contrario.
        return $deleted;
    }
}
