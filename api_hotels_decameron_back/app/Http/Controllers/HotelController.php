<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreHotelRequest;
use App\Http\Requests\UpdateHotelRequest;
use App\Services\HotelServiceInterface;
use Illuminate\Http\JsonResponse;

class HotelController extends Controller
{
    private HotelServiceInterface $hotelService;

    /**
     * Inyectamos el servicio de hoteles en el constructor.
     */
    public function __construct(HotelServiceInterface $hotelService)
    {
        $this->hotelService = $hotelService;
    }

    /**
     * Muestra una lista de todos los hoteles.
     */
    public function index(): JsonResponse
    {
        $hotels = $this->hotelService->getAll();
        return response()->json($hotels);
    }

    /**
     * Muestra un hotel específico por su ID.
     */
    public function show(int $id): JsonResponse
    {
        $hotel = $this->hotelService->findById($id);

        if (!$hotel) {
            return response()->json(['message' => 'Hotel not found'], 404);
        }

        return response()->json($hotel);
    }

    /**
     * Almacena un hotel recién creado.
     * La validación se maneja en StoreHotelRequest.
     */
    public function store(StoreHotelRequest $request): JsonResponse
    {
        $validatedData = $request->validated();
        $hotel = $this->hotelService->create($validatedData);
        return response()->json($hotel, 201);
    }

    /**
     * Actualiza un hotel específico en el almacenamiento.
     * La validación se maneja en UpdateHotelRequest.
     */
    public function update(UpdateHotelRequest $request, int $id): JsonResponse
    {
        $validatedData = $request->validated();
        $hotel = $this->hotelService->update($id, $validatedData);

        if (!$hotel) {
            return response()->json(['message' => 'Hotel not found'], 404);
        }

        return response()->json($hotel);
    }

    /**
     * Elimina un hotel específico del almacenamiento.
     */
    public function destroy(int $id): JsonResponse
    {
        $deleted = $this->hotelService->delete($id);

        if (!$deleted) {
            return response()->json(['message' => 'Hotel not found or could not be deleted'], 404);
        }

        return response()->json(null, 204);
    }
}
