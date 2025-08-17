<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreHotelRequest extends FormRequest
{
    /**
     * Determina si el usuario está autorizado para hacer esta petición.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Obtiene las reglas de validación que se aplican a la petición.
     */
    public function rules(): array
    {
        return [
            // Validación de los datos del hotel
            'name' => ['required', 'string', 'max:255'],
            'address' => ['required', 'string', 'max:255'],
            'nit' => ['required', 'string', 'max:20', 'min:5', 'unique:hotels,nit'],
            'rooms_total' => ['required', 'integer', 'min:1'],
            'city_id' => ['required', 'exists:cities,id'],

            // Validación de las configuraciones de habitaciones.
            'room_configurations' => ['required', 'array'],
            'room_configurations.*.room_type' => ['required', 'string', 'max:255'],
            'room_configurations.*.accommodation' => ['required', 'string', 'max:255'],
            'room_configurations.*.quantity' => ['required', 'integer', 'min:1'],
        ];
    }

    /**
     * Valida que la suma de las habitaciones coincida con rooms_total.
     */
    public function after(): array
    {
        return [
            function ($validator) {
                $roomConfigurations = $this->input('room_configurations');

                // Verificación defensiva para evitar el TypeError
                if (!is_array($roomConfigurations)) {
                    return;
                }

                $roomsTotal = (int) $this->input('rooms_total');
                $totalRoomQuantity = array_sum(array_column($roomConfigurations, 'quantity'));

                if ($totalRoomQuantity !== $roomsTotal) {
                    $validator->errors()->add(
                        'rooms_total',
                        'La suma de las cantidades de habitaciones no coincide con la cantidad total de habitaciones del hotel.'
                    );
                }
            }
        ];
    }
}
