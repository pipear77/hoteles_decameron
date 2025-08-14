<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreHotelRoomRequest extends FormRequest
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
        return [
            // Valida que el ID del tipo de habitación y la acomodación no existan juntos para el mismo hotel.
            'room_type_id' => [
                'required',
                'exists:room_types,id',
                Rule::unique('hotel_rooms')->where(function ($query) {
                    return $query->where('hotel_id', $this->route('hotelId'))
                        ->where('accommodation_id', $this->input('accommodation_id'));
                }),
            ],
            // Valida que el ID de la acomodación exista.
            'accommodation_id' => [
                'required',
                'exists:accommodations,id',
            ],
            // Valida que la cantidad de habitaciones sea un entero requerido.
            'quantity' => [
                'required',
                'integer',
                'min:1',
            ],
        ];
    }
}
