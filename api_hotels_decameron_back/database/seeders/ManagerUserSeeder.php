<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class ManagerUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // El método firstOrCreate evita duplicados si el seeder se ejecuta múltiples veces.
        // Se crean 2 usuarios con roles específicos para testeo.
        User::firstOrCreate(
            ['email' => 'gerente@example.com'],
            [
                'first_name' => 'Gerente Hotelero',
                'last_name' => 'Gerente',
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
                'remember_token' => Str::random(10),
            ]
        );
    }
}
