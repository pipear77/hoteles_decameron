<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'first_name' => 'Admin',
            'last_name' => 'Principal',
            'email' => 'admin@example.com',
            'password' => Hash::make('password123'),
            'role_id' => 1 // Asume que admin es ID 1
        ]);

        User::create([
            'first_name' => 'Usuario',
            'last_name' => 'Prueba',
            'email' => 'user@example.com',
            'password' => Hash::make('password123'),
            'role_id' => 2 // Asume que user es ID 2
        ]);
    }
}
