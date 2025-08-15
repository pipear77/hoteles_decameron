<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateAccommodationRequest extends FormRequest
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
        // El campo 'name' es opcional ('sometimes') al actualizar, pero único si se envía.
        // Usamos Rule::unique para evitar conflictos con el mismo registro.
        return [
            'name' => [
                'sometimes', // Solo valida si el campo está presente en la petición
                'string',
                'max:255',
                Rule::unique('accommodations')->ignore($this->route('accommodation')),
            ],
            // El campo 'description' es opcional y se valida si está presente.
            'description' => ['sometimes', 'string', 'max:500'],
        ];
    }
}
