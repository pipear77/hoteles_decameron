<?php

namespace Database\Factories;

use App\Models\City;
use App\Models\Hotel;
use App\Models\User; // Importa el modelo User
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Hotel>
 */
class HotelFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Hotel::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->company . ' Hotel',
            'address' => $this->faker->address,
            'nit' => $this->faker->unique()->numerify('##########'),
            'rooms_total' => $this->faker->numberBetween(10, 200),
            'city_id' => City::factory(),
            // Agrega la clave foránea para el usuario
            'user_id' => User::factory(),
        ];
    }
}
