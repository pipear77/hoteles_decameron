<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateHotelRequest extends FormRequest
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
        /// Si viene un objeto Hotel por route model binding, saco su id
        $hotelParam = $this->route('hotel');
        $hotelId = $hotelParam instanceof \App\Models\Hotel ? $hotelParam->id : $hotelParam;

        return [
            // Las reglas se aplican solo si el campo está presente ('sometimes').
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'address' => ['sometimes', 'required', 'string', 'max:255'],
            // La validación del NIT ignora el hotel que se está actualizando.
            'nit' => ['sometimes', 'required', 'string', 'max:10', 'min:10',
                Rule::unique('hotels', 'nit')->ignore($hotelId, 'id')],
            'rooms_total' => ['sometimes', 'required', 'integer', 'min:1'],
            'email' => ['sometimes', 'required', 'email', 'max:255'],
            'city_id' => ['sometimes', 'required', 'exists:cities,id'],

            // Validación de las configuraciones de habitaciones.
            'room_configurations' => ['sometimes', 'required', 'array'],
            'room_configurations.*.room_type_id' => ['sometimes', 'required', 'integer', 'max:255'],
            'room_configurations.*.accommodation_id' => ['sometimes', 'required', 'integer', 'max:255'],
            'room_configurations.*.quantity' => ['sometimes', 'required', 'integer', 'min:1'],
        ];
    }

    /**
     * Valida que la suma de las habitaciones coincida con rooms_total, si 'rooms_total' está presente.
     */
    public function after(): array
    {
        return [
            function ($validator) {
                // Solo se ejecuta la validación si 'rooms_total' y 'room_configurations' están presentes
                if ($this->has('rooms_total') && $this->has('room_configurations')) {
                    $roomConfigurations = $this->input('room_configurations');
                    $roomsTotal = (int) $this->input('rooms_total');
                    $totalRoomQuantity = array_sum(array_column($roomConfigurations, 'quantity'));

                    if ($totalRoomQuantity !== $roomsTotal) {
                        $validator->errors()->add(
                            'rooms_total',
                            'La suma de las cantidades de habitaciones no coincide con la cantidad total de habitaciones del hotel.'
                        );
                    }
                }
            }
        ];
    }
}
