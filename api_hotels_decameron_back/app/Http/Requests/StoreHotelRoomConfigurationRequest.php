<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreHotelRoomConfigurationRequest extends FormRequest
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
        return [
            'room_type_id' => [
                'required',
                'exists:room_types,id',
                // Asegura una combinación única de tipo de habitación y acomodación por hotel.
                Rule::unique('hotel_room_configurations')
                    ->where('hotel_id', $this->route('hotelId'))
                    ->where('accommodation_id', $this->input('accommodation_id')),
            ],
            'accommodation_id' => [
                'required',
                'exists:accommodations,id',
            ],
            'quantity' => [
                'required',
                'integer',
                'min:1',
            ],
        ];
    }
}
