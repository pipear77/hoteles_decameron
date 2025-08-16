<?php

namespace Database\Factories;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * El nombre del modelo correspondiente al factory.
     *
     * @var string
     */
    protected $model = User::class;

    /**
     * Define el estado por defecto del modelo.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Se define un valor por defecto para 'role_id' para garantizar
        // que la relación con el rol sea siempre consistente.
        // Esto simplifica la creación de usuarios en los tests.
        $role = Role::factory()->create();

        return [
            'first_name' => $this->faker->firstName,
            'last_name' => $this->faker->lastName,
            'email' => $this->faker->unique()->safeEmail,
            'email_verified_at' => now(),
            'password' => Hash::make('password'), // Se asegura de que la contraseña esté presente y hasheada
            'remember_token' => Str::random(10),
            'role_id' => Role::factory(), // Asocia el usuario a un rol recién creado
        ];
    }
}
