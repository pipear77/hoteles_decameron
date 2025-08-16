<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Recurso simple para la relación de la ciudad.
 * Se define aquí mismo para mantener la modularidad y la coherencia en un solo archivo.
 */
class CityResource extends JsonResource
{
    /**
     * Transforma el recurso de la ciudad en un array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
        ];
    }
}
