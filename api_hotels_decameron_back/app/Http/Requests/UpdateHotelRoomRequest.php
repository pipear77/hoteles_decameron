<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateHotelRoomRequest extends FormRequest
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
            // La validación de unicidad excluye la configuración de habitación actual.
            'room_type_id' => [
                'sometimes',
                'exists:room_types,id',
                Rule::unique('hotel_rooms')->ignore($this->route('hotelRoomId'))
                    ->where(function ($query) {
                        return $query->where('hotel_id', $this->route('hotelId'))
                            ->where('accommodation_id', $this->input('accommodation_id'));
                    }),
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
