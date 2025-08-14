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
        return User::all();
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
            // Si la contraseña está presente en la petición, la hasheamos.
            if (isset($data['password'])) {
                $data['password'] = Hash::make($data['password']);
            }
            // Eloquent actualizará solo los campos presentes en el array $data.
            $user->update($data);
        }

        return $user;
    }
}
