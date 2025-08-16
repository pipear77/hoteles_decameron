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
        // Accede a la configuración que se está actualizando a través de la ruta
        $configurationId = $this->route('room_configuration')->id;
        $hotelId = $this->route('hotel')->id;

        return [
            'room_type_id' => [
                'required',
                'exists:room_types,id',
                // La regla 'unique' debe ignorar el ID actual y estar restringida al hotel_id
                'unique:hotel_room_configurations,room_type_id,' . $configurationId . ',id,hotel_id,' . $hotelId
            ],
            'quantity' => ['required', 'integer', 'min:1'],
        ];
    }
}
