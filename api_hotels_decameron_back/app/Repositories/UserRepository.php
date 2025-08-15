<?php

namespace App\Repositories;

use App\Models\User;
use App\Repositories\UserRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Hash;

class UserRepository implements UserRepositoryInterface
{
    /**
     * Obtener todos los usuarios.
     *
     * @return Collection<int, User>
     */
    public function all(): Collection
    {
        // Se carga la relación 'role' para evitar el problema de N+1
        // y tener el rol disponible al obtener todos los usuarios.
        return User::with('role')->get();
    }

    /**
     * Encontrar un usuario por su ID.
     *
     * @param int $id
     * @return User|null
     */
    public function find(int $id): ?User
    {
        return User::find($id);
    }

    /**
     * Encontrar un usuario por su email.
     *
     * @param string $email
     * @return User|null
     */
    public function findByEmail(string $email): ?User
    {
        return User::where('email', $email)->first();
    }

    /**
     * Crear un nuevo usuario.
     *
     * @param array $data
     * @return User
     */
    public function create(array $data): User
    {
        return User::create($data);
    }

    /**
     * Actualiza un usuario por su ID.
     *
     * @param int $id
     * @param array $data
     * @return User|null
     */
    public function update(int $id, array $data): ?User
    {
        $user = $this->find($id);

        if ($user) {
            // El servicio ya se encarga de hashear la contraseña.
            // La lógica de negocio está en el lugar correcto.
            $user->update($data);
        }

        return $user;
    }

    /**
     * Elimina un usuario por su ID.
     *
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool
    {
        $user = $this->find($id);
        if ($user) {
            return $user->delete();
        }

        return false;
    }
}
