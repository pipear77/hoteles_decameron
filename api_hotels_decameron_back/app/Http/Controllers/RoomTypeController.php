<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRoomTypeRequest;
use App\Http\Requests\UpdateRoomTypeRequest;
use App\Services\RoomTypeServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RoomTypeController extends Controller
{
    private RoomTypeServiceInterface $roomTypeService;

    public function __construct(RoomTypeServiceInterface $roomTypeService)
    {
        $this->roomTypeService = $roomTypeService;
    }

    /**
     * Muestra una lista de todos los tipos de habitación.
     */
    public function index(): JsonResponse
    {
        $roomTypes = $this->roomTypeService->getAll();
        return response()->json($roomTypes);
    }

    /**
     * Muestra un tipo de habitación específico.
     */
    public function show(int $id): JsonResponse
    {
        $roomType = $this->roomTypeService->findById($id);

        if (!$roomType) {
            return response()->json(['message' => 'Room type not found'], 404);
        }

        return response()->json($roomType);
    }

    /**
     * Almacena un nuevo tipo de habitación.
     */
    public function store(StoreRoomTypeRequest $request): JsonResponse
    {
        $roomType = $this->roomTypeService->create($request->validated());
        return response()->json($roomType, 201);
    }

    /**
     * Actualiza un tipo de habitación específico.
     */
    public function update(UpdateRoomTypeRequest $request, int $id): JsonResponse
    {
        $roomType = $this->roomTypeService->update($id, $request->validated());

        if (!$roomType) {
            return response()->json(['message' => 'Room type not found'], 404);
        }

        return response()->json($roomType);
    }

    /**
     * Elimina un tipo de habitación específico.
     */
    public function destroy(int $id): JsonResponse
    {
        $deleted = $this->roomTypeService->delete($id);

        if (!$deleted) {
            return response()->json(['message' => 'Room type not found or could not be deleted'], 404);
        }

        return response()->json(null, 204);
    }
}
