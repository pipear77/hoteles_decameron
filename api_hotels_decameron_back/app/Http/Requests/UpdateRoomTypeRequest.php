<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateRoomTypeRequest extends FormRequest
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
            'name' => [
                'sometimes', // Solo valida si el campo está presente en la petición
                'string',
                'max:255',
                Rule::unique('room_types')->ignore($this->route('room_type')),
            ],
            // El campo 'description' es opcional y se valida si está presente.
            'description' => ['sometimes', 'string', 'max:500'],
        ];
    }
}
