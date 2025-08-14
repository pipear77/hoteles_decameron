<?php

namespace Database\Seeders;

use App\Models\Accommodation;
use Illuminate\Database\Seeder;

class AccommodationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Accommodation::truncate();

        $accommodations = [
            ['name' => 'SENCILLA', 'description' => 'Acomodación para una persona.'],
            ['name' => 'DOBLE', 'description' => 'Acomodación para dos personas.'],
            ['name' => 'TRIPLE', 'description' => 'Acomodación para tres personas.'],
            ['name' => 'CUADRUPLE', 'description' => 'Acomodación para cuatro personas.'],
        ];

        foreach ($accommodations as $accommodation) {
            Accommodation::create($accommodation);
        }
    }
}
