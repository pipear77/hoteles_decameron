<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreHotelRoomConfigurationRequest;
use App\Http\Requests\UpdateHotelRoomConfigurationRequest;
use App\Services\HotelRoomConfigurationServiceInterface;
use Illuminate\Http\JsonResponse;

class HotelRoomConfigurationController extends Controller
{
    public function __construct(
        private HotelRoomConfigurationServiceInterface $hotelRoomConfigurationService
    ) {}

    /**
     * Muestra una lista de todas las configuraciones de habitaciones para un hotel específico.
     *
     * @param int $hotelId
     * @return JsonResponse
     */
    public function index(int $hotelId): JsonResponse
    {
        $hotelRooms = $this->hotelRoomConfigurationService->getByHotelId($hotelId);
        return response()->json($hotelRooms);
    }

    /**
     * Muestra una configuración de habitación específica por su ID.
     *
     * @param int $hotelId
     * @param int $hotelRoomConfigurationId
     * @return JsonResponse
     */
    public function show(int $hotelId, int $hotelRoomConfigurationId): JsonResponse
    {
        $hotelRoom = $this->hotelRoomConfigurationService->find($hotelRoomConfigurationId);

        // Se agrega un filtro para asegurar que la configuración pertenezca al hotel correcto
        if (!$hotelRoom || $hotelRoom->hotel_id !== $hotelId) {
            return response()->json(['message' => 'Hotel room configuration not found or does not belong to this hotel.'], 404);
        }

        return response()->json($hotelRoom);
    }

    /**
     * Almacena una nueva configuración de habitación.
     *
     * @param StoreHotelRoomConfigurationRequest $request
     * @param int $hotelId
     * @return JsonResponse
     */
    public function store(StoreHotelRoomConfigurationRequest $request, int $hotelId): JsonResponse
    {
        $validatedData = $request->validated();
        $hotelRoomConfiguration = $this->hotelRoomConfigurationService->create($hotelId, $validatedData);
        return response()->json($hotelRoomConfiguration, 201);
    }

    /**
     * Actualiza una configuración de habitación específica.
     *
     * @param UpdateHotelRoomConfigurationRequest $request
     * @param int $hotelId
     * @param int $hotelRoomConfigurationId
     * @return JsonResponse
     */
    public function update(UpdateHotelRoomConfigurationRequest $request, int $hotelId, int $hotelRoomConfigurationId): JsonResponse
    {
        $validatedData = $request->validated();
        $hotelRoomConfiguration = $this->hotelRoomConfigurationService->update($hotelRoomConfigurationId, $validatedData);

        if (!$hotelRoomConfiguration) {
            return response()->json(['message' => 'Hotel room configuration not found.'], 404);
        }

        // Se agrega un filtro para asegurar que la configuración pertenezca al hotel correcto
        if ($hotelRoomConfiguration->hotel_id !== $hotelId) {
            return response()->json(['message' => 'Hotel room configuration does not belong to this hotel.'], 403);
        }

        return response()->json($hotelRoomConfiguration);
    }

    /**
     * Elimina una configuración de habitación específica.
     *
     * @param int $hotelId
     * @param int $hotelRoomConfigurationId
     * @return JsonResponse
     */
    public function destroy(int $hotelId, int $hotelRoomConfigurationId): JsonResponse
    {
        $hotelRoomConfiguration = $this->hotelRoomConfigurationService->find($hotelRoomConfigurationId);

        if (!$hotelRoomConfiguration) {
            return response()->json(['message' => 'Hotel room configuration not found.'], 404);
        }

        if ($hotelRoomConfiguration->hotel_id !== $hotelId) {
            return response()->json(['message' => 'Hotel room configuration does not belong to this hotel.'], 403);
        }

        $deleted = $this->hotelRoomConfigurationService->delete($hotelRoomConfigurationId);

        if (!$deleted) {
            // Un 500 es más apropiado si el recurso existe pero no se pudo eliminar.
            return response()->json(['message' => 'Hotel room configuration could not be deleted.'], 500);
        }

        return response()->json(null, 204);
    }
}
