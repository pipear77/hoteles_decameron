<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\UserRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class UserService implements UserServiceInterface
{
    public function __construct(
        private UserRepositoryInterface $repository
    ) {}

    /**
     * Obtener todos los usuarios.
     *
     * @return Collection<int, User>
     */
    public function getAllUsers(): Collection
    {
        return $this->repository->all();
    }

    /**
     * Registrar un nuevo usuario.
     *
     * @param array $userData
     * @return User
     */
    public function registerUser(array $userData): User
    {
        // Encriptar la contraseña antes de pasarla al repositorio
        $userData['password'] = Hash::make($userData['password']);
        return $this->repository->create($userData);
    }

    /**
     * Autentica a un usuario usando las credenciales proporcionadas.
     *
     * @param array $credentials
     * @return array
     */
    public function authenticateUser(array $credentials): array
    {
        // Usamos Auth::attempt() para delegar toda la lógica de autenticación
        // a Laravel. Es la forma más segura y estandarizada.
        if (Auth::attempt($credentials)) {
            $user = Auth::user();

            // Si el login es exitoso, generamos el token.
            // Primero, eliminamos los tokens antiguos para mantener el sistema limpio.
            // Si quieres que el usuario pueda tener varios tokens, quita esta línea.
            $user->tokens()->delete();
            $token = $user->createToken('auth-token')->plainTextToken;

            return [
                'status' => true,
                'message' => '¡Inicio de sesión exitoso!',
                'token' => $token
            ];
        }

        // Si falla, devolvemos una respuesta clara para el controlador.
        return [
            'status' => false,
            'message' => 'Credenciales incorrectas.',
        ];
    }

    /**
     * Asigna roles a un usuario específico.
     *
     * @param int $userId
     * @param array $roleIds
     * @return User|null
     */
    public function assignRoles(int $userId, array $roleIds): ?User
    {
        $user = $this->repository->find($userId);

        if ($user) {
            $user->roles()->sync($roleIds);
        }

        return $user;
    }


    /**
     * Busca un usuario por su ID.
     *
     * @param int $id
     * @return User|null
     */
    public function findUserById(int $id): ?User
    {
        return $this->repository->find($id);
    }

    /**
     * Actualiza un usuario por su ID.
     *
     * @param int $id
     * @param array $userData
     * @return User|null
     */
    public function updateUser(int $id, array $userData): ?User
    {
        // El repositorio manejará la lógica de búsqueda y actualización.
        return $this->repository->update($id, $userData);
    }
}
