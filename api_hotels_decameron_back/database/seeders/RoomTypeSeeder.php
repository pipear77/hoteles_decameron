<?php

namespace Database\Seeders;

use App\Models\RoomType;
use Illuminate\Database\Seeder;

class RoomTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        RoomType::truncate();

        $types = [
            ['name' => 'ESTANDAR', 'description' => 'Habitación estándar.'],
            ['name' => 'JUNIOR', 'description' => 'Habitación Junior.'],
            ['name' => 'SUITE', 'description' => 'Habitación Suite.'],
        ];

        foreach ($types as $type) {
            RoomType::create($type);
        }
    }
}
