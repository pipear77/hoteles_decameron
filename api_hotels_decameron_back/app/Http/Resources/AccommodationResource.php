<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 * schema="AccommodationResource",
 * title="AccommodationResource",
 * description="Representación de un recurso de alojamiento",
 * @OA\Property(
 * property="id",
 * type="integer",
 * description="ID único del alojamiento"
 * ),
 * @OA\Property(
 * property="name",
 * type="string",
 * description="Nombre del alojamiento"
 * ),
 * @OA\Property(
 * property="description",
 * type="string",
 * description="Descripción del alojamiento"
 * ),
 * )
 */
class AccommodationResource extends JsonResource
{
    /**
     * Transforma el recurso en una matriz.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // Al usar 'JsonResource', el modelo se accede con $this.
        // Aquí definimos la estructura exacta del JSON que se enviará al cliente.
        // Esto asegura que el `id` siempre esté presente en las respuestas.
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
        ];
    }
}
