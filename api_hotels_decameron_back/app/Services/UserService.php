<?php

namespace App\Services;

use App\Models\Role;
use App\Models\User;
use App\Repositories\UserRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

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
        // Obtiene el rol 'user' para asignarlo por defecto.
        $userRole = Role::where('name', 'user')->first();

        if (!$userRole) {
            // Si el rol no existe, lanza una excepción.
            throw new \Exception('El rol "user" no se encuentra en la base de datos. Asegúrate de ejecutar los seeders.');
        }

        $userData['password'] = Hash::make($userData['password']);
        $userData['role_id'] = $userRole->id;

        return $this->repository->create($userData);
    }

    /**
     * Autentica a un usuario y genera un token con abilities.
     *
     * @param array $credentials
     * @return array
     * @throws ValidationException
     */
    public function authenticateUser(array $credentials): array
    {
        $user = $this->repository->findByEmail($credentials['email']);

        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Las credenciales proporcionadas son incorrectas.'],
            ]);
        }

        // Primero, eliminamos los tokens antiguos para mantener el sistema limpio.
        $user->tokens()->delete();

        // Obtiene el nombre del rol del usuario.
        $roleName = $user->role->name;

        // Define las abilities (habilidades) basadas en el rol.
        $abilities = ($roleName === 'admin') ? ['admin:all'] : ['user:view'];

        // Crea el token con las abilities correctas.
        $token = $user->createToken('auth-token', $abilities)->plainTextToken;

        return [
            'status' => true,
            'message' => 'Login exitoso',
            'token' => $token,
            'user' => $user->load('role'),
        ];
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
        // Encriptar la contraseña si se proporciona.
        if (isset($userData['password'])) {
            $userData['password'] = Hash::make($userData['password']);
        }
        return $this->repository->update($id, $userData);
    }

    /**
     * Elimina un usuario por su ID.
     *
     * @param int $id
     * @return bool
     */
    public function deleteUser(int $id): bool
    {
        return $this->repository->delete($id);
    }

    /**
     * Asigna un nuevo rol a un usuario, con validación de lógica de negocio.
     *
     * @param int $userId El ID del usuario a modificar.
     * @param int $newRoleId El ID del nuevo rol.
     * @return User|null
     * @throws NotFoundHttpException Si el usuario no existe.
     * @throws AccessDeniedHttpException Si el usuario intenta cambiar su propio rol o si no tiene permisos.
     */
    public function updateUserRole(int $userId, int $newRoleId): ?User
    {
        $user = $this->repository->find($userId);

        if (!$user) {
            throw new NotFoundHttpException('Usuario no encontrado.');
        }

        // Verifica si el usuario actual intenta cambiar su propio rol.
        if (Auth::id() === $user->id) {
            throw new AccessDeniedHttpException('No puedes cambiar tu propio rol.');
        }

        // Verifica si el rol a asignar es 'admin' y si el usuario actual tiene permisos.
        $newRole = Role::find($newRoleId);
        if ($newRole && $newRole->name === 'admin' && Auth::user()->role->name !== 'admin') {
            throw new AccessDeniedHttpException('No tienes permiso para asignar el rol de administrador.');
        }

        $user->role_id = $newRoleId;
        $user->save();

        return $user;
    }
}
