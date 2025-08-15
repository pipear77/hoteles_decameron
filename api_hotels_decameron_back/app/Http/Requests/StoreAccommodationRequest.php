<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAccommodationRequest extends FormRequest
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
        // El campo 'name' es requerido y único al crear un registro.
        return [
            'name' => ['required', 'string', 'max:255', 'unique:accommodations,name'],
            'description' => ['nullable', 'string', 'max:500'], // La descripción puede ser opcional
        ];

    }
}
