<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreHotelRoomConfigurationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        // Se obtiene el ID del hotel de la ruta o del input, para mayor robustez.
        $hotelId = $this->route('hotel') ? $this->route('hotel')->id : $this->input('hotel_id');

        return [
            'room_type_id' => [
                'required',
                'exists:room_types,id',
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
