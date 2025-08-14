<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreHotelRoomRequest;
use App\Http\Requests\UpdateHotelRoomRequest;
use App\Services\HotelRoomServiceInterface;
use Illuminate\Http\JsonResponse;

class HotelRoomController extends Controller
{
    private HotelRoomServiceInterface $hotelRoomService;

    public function __construct(HotelRoomServiceInterface $hotelRoomService)
    {
        $this->hotelRoomService = $hotelRoomService;
    }

    /**
     * Muestra una lista de todas las configuraciones de habitaciones para un hotel específico.
     */
    public function index(int $hotelId): JsonResponse
    {
        $hotelRooms = $this->hotelRoomService->getByHotelId($hotelId);
        return response()->json($hotelRooms);
    }

    /**
     * Muestra una configuración de habitación específica por su ID.
     */
    public function show(int $hotelRoomId): JsonResponse
    {
        $hotelRoom = $this->hotelRoomService->findById($hotelRoomId);

        if (!$hotelRoom) {
            return response()->json(['message' => 'Hotel room configuration not found'], 404);
        }

        return response()->json($hotelRoom);
    }

    /**
     * Almacena una nueva configuración de habitación.
     * La validación se maneja en StoreHotelRoomRequest.
     */
    public function store(StoreHotelRoomRequest $request, int $hotelId): JsonResponse
    {
        $validatedData = $request->validated();
        $validatedData['hotel_id'] = $hotelId;

        $hotelRoom = $this->hotelRoomService->create($validatedData);
        return response()->json($hotelRoom, 201);
    }

    /**
     * Actualiza una configuración de habitación específica.
     * La validación se maneja en UpdateHotelRoomRequest.
     */
    public function update(UpdateHotelRoomRequest $request, int $hotelId, int $hotelRoomId): JsonResponse
    {
        $validatedData = $request->validated();
        $hotelRoom = $this->hotelRoomService->update($hotelRoomId, $validatedData);

        if (!$hotelRoom) {
            return response()->json(['message' => 'Hotel room configuration not found'], 404);
        }

        return response()->json($hotelRoom);
    }

    /**
     * Elimina una configuración de habitación específica.
     */
    public function destroy(int $hotelRoomId): JsonResponse
    {
        $deleted = $this->hotelRoomService->delete($hotelRoomId);

        if (!$deleted) {
            return response()->json(['message' => 'Hotel room configuration not found or could not be deleted'], 404);
        }

        return response()->json(null, 204);
    }
}
