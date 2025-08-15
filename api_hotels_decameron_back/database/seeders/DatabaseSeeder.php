<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RoleSeeder::class, // Se debe ejecutar primero para crear los roles antes que los users
            UserSeeder::class,
            AccommodationSeeder::class,
            RoomTypeSeeder::class,
        ]);
    }
}
