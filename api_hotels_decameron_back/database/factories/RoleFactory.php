<?php

namespace Database\Factories;

use App\Models\Role;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Role>
 */
class RoleFactory extends Factory
{
    /**
     * El nombre del modelo correspondiente al factory.
     *
     * @var string
     */
    protected $model = Role::class;

    /**
     * Define el estado por defecto del modelo.
     * La lógica aquí es simple: solo genera un nombre de rol único.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->word,
        ];
    }
}
