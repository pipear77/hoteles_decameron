<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        // Se definen las ciudades básicas para las pruebas.
        // Se incluyen los campos 'name', 'country', 'created_at', y 'updated_at'
        // para que coincidan con la estructura de la tabla 'cities'.
        $cities = [
            [
                'name' => 'CARTAGENA',
                'country' => 'Colombia',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'BOGOTA',
                'country' => 'Colombia',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'MEDELLIN',
                'country' => 'Colombia',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'ARMENIA',
                'country' => 'Colombia',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ];

        // Se insertan los datos en la tabla 'cities' de forma masiva
        // para un rendimiento óptimo.
        DB::table('cities')->insert($cities);
    }
}
