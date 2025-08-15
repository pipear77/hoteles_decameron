<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        // Limpia la tabla de usuarios para evitar duplicados en cada ejecución.
        User::truncate();

        // Obtiene el rol de 'admin' para asegurar la asignación correcta.
        $adminRole = Role::where('name', 'admin')->first();

        // Verifica que el rol de admin exista antes de crear el usuario.
        if ($adminRole) {
            User::create([
                'first_name' => 'Admin',
                'last_name' => 'Principal',
                'email' => 'admin@example.com',
                'password' => Hash::make('password123'),
                'role_id' => $adminRole->id,
            ]);
        } else {
            // Manejo de error si el rol de admin no se encuentra, lo que indica un problema de orden en los seeders.
            $this->command->error('El rol "admin" no fue encontrado. Asegúrate de ejecutar el RoleSeeder primero.');
        }
    }
}
