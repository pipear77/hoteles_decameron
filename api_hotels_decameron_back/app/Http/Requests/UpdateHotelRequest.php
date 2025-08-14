<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateHotelRequest extends FormRequest
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
            // 'name' es opcional, pero si se envía, debe ser único, excluyendo el hotel actual.
            'name' => ['string', 'max:255', 'unique:hotels,name,' . $this->route('hotel')],
            'address' => ['string', 'max:255'],
            'city' => ['string', 'max:255'],
            // 'nit' es opcional, pero si se envía, debe ser único, excluyendo el hotel actual.
            'nit' => ['string', 'max:20', 'unique:hotels,nit,' . $this->route('hotel')],
            'rooms_total' => ['integer', 'min:1'],
        ];
    }
}
