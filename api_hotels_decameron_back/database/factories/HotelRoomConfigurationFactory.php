<?php

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
     * Define el nombre del modelo asociado al factory.
     *
     * @var string
     */
    protected $model = HotelRoomConfiguration::class;

    /**
     * Define el estado por defecto del modelo.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            // Se asocia con un Hotel aleatorio o uno nuevo.
            // Para la mayoría de los tests, crear una nueva instancia es suficiente.
            'hotel_id' => Hotel::factory(),

            // Se asocia con un RoomType aleatorio o uno nuevo.
            'room_type_id' => RoomType::factory(),

            // Se asocia con una Accommodation aleatoria o una nueva.
            // Es vital que el modelo `Accommodation` y su factory existan.
            'accommodation_id' => Accommodation::factory(),

            // Cantidad de habitaciones de este tipo, entre 1 y 20.
            'quantity' => $this->faker->numberBetween(1, 20),
        ];
    }
}
