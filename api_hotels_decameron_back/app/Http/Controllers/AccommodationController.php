<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAccommodationRequest;
use App\Http\Requests\UpdateAccommodationRequest;
use App\Services\AccommodationServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AccommodationController extends Controller
{
    private AccommodationServiceInterface $accommodationService;

    public function __construct(AccommodationServiceInterface $accommodationService)
    {
        $this->accommodationService = $accommodationService;
    }

    /**
     * Muestra una lista de todas las acomodaciones.
     */
    public function index(): JsonResponse
    {
        $accommodations = $this->accommodationService->getAll();
        return response()->json($accommodations);
    }

    /**
     * Muestra una acomodación específica.
     */
    public function show(int $id): JsonResponse
    {
        $accommodation = $this->accommodationService->findById($id);

        if (!$accommodation) {
            return response()->json(['message' => 'Accommodation not found'], 404);
        }

        return response()->json($accommodation);
    }

    /**
     * Almacena una nueva acomodación.
     */
    public function store(StoreAccommodationRequest $request): JsonResponse
    {
        $accommodation = $this->accommodationService->create($request->validated());
        return response()->json($accommodation, 201);
    }

    /**
     * Actualiza una acomodación específica.
     */
    public function update(UpdateAccommodationRequest $request, int $id): JsonResponse
    {
        $accommodation = $this->accommodationService->update($id, $request->validated());

        if (!$accommodation) {
            return response()->json(['message' => 'Accommodation not found'], 404);
        }

        return response()->json($accommodation);
    }

    /**
     * Elimina una acomodación específica.
     */
    public function destroy(int $id): JsonResponse
    {
        $deleted = $this->accommodationService->delete($id);

        if (!$deleted) {
            return response()->json(['message' => 'Accommodation not found or could not be deleted'], 404);
        }

        return response()->json(null, 204);
    }
}
