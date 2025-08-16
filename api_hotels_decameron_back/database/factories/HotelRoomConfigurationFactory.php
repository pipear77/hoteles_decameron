<?php
// database/factories/HotelRoomConfigurationFactory.php

namespace Database\Factories;

use App\Models\Accommodation;
use App\Models\Hotel;
use App\Models\HotelRoomConfiguration;
use App\Models\RoomType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<HotelRoomConfiguration>
 */
class HotelRoomConfigurationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            // Por defecto, se crean las relaciones necesarias para que el modelo sea válido.
            // Esto evita errores de 'NOT NULL constraint' en las pruebas.
            'hotel_id' => Hotel::factory(),
            'room_type_id' => RoomType::factory(),
            'accommodation_id' => Accommodation::factory(),
            'quantity' => $this->faker->numberBetween(1, 20),
        ];
    }
}
