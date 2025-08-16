<?php

namespace Database\Factories;

use App\Models\Hotel;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<Hotel>
 */
class HotelFactory extends Factory
{
    /**
     * Define el nombre del modelo asociado al factory.
     *
     * @var string
     */
    protected $model = Hotel::class;

    /**
     * Define el estado por defecto del modelo.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Genera un nombre de ciudad único para asegurar que el par (name, city) sea único.
        // Se utiliza un prefijo para evitar colisiones con nombres de ciudades reales en caso de un gran número de creaciones.
        $cityName = $this->faker->unique()->city;

        return [
            // Se usa el nombre de ciudad generado arriba para crear un nombre de hotel único.
            'name' => $cityName . ' Hotel',
            // La dirección es un dato aleatorio, pero realista.
            'address' => $this->faker->address,
            // La ciudad se extrae de la variable previamente generada para garantizar la unicidad del par.
            'city' => $cityName,
            // 'country' por defecto a 'Colombia' según la migración. Se puede sobrescribir si es necesario.
            'country' => 'Colombia',
            // El 'nit' se genera como un número de 9 dígitos único para cumplir con la restricción.
            'nit' => $this->faker->unique()->randomNumber(9, true),
            // El número de habitaciones totales es un entero entre 10 y 500.
            'rooms_total' => $this->faker->numberBetween(10, 500),
        ];
    }
}
