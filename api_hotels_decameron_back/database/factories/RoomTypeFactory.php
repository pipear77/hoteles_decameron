<?php

namespace Database\Factories;

use App\Models\RoomType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<RoomType>
 */
class RoomTypeFactory extends Factory
{
    /**
     * Define el nombre del modelo asociado al factory.
     *
     * @var string
     */
    protected $model = RoomType::class;

    /**
     * Define el estado por defecto del modelo.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            // El 'name' se genera de forma única para cumplir con la restricción de la base de datos.
            'name' => $this->faker->unique()->word . ' ' . $this->faker->word,
            // La 'description' es opcional, pero es buena práctica poblarla para pruebas.
            'description' => $this->faker->sentence(10),
        ];
    }
}
