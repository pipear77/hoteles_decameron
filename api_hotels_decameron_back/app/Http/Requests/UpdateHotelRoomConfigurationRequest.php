<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateHotelRoomConfigurationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        // Usa la tabla con el nombre corregido.
        // La validación de unicidad ignora la configuración de habitación actual.
        $hotelRoomConfigurationId = $this->route('hotelRoomConfigurationId');

        return [
            'room_type_id' => [
                'sometimes',
                'exists:room_types,id',
                // Asegura la unicidad, ignorando la configuración actual.
                Rule::unique('hotel_room_configurations')
                    ->ignore($hotelRoomConfigurationId)
                    ->where('hotel_id', $this->route('hotelId'))
                    ->where('accommodation_id', $this->input('accommodation_id')),
            ],
            'accommodation_id' => [
                'sometimes',
                'exists:accommodations,id',
            ],
            'quantity' => [
                'sometimes',
                'integer',
                'min:1',
            ],
        ];
    }
}
