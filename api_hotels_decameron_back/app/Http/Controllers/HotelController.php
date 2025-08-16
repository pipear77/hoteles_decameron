<?php
// src/app/Http/Controllers/HotelController.php

namespace App\Http\Controllers;

use App\Http\Requests\StoreHotelRequest;
use App\Http\Requests\UpdateHotelRequest;
use App\Http\Resources\HotelResource;
use App\Services\HotelServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // Importa la Facade de Auth
use Exception;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class HotelController extends Controller
{
    /**
     * @var HotelServiceInterface
     */
    protected HotelServiceInterface $hotelService;

    /**
     * Constructor del controlador.
     * Inyecta la interfaz del servicio para seguir el principio de Inversión de Dependencias (DIP).
     *
     * @param HotelServiceInterface $hotelService
     */
    public function __construct(HotelServiceInterface $hotelService)
    {
        $this->hotelService = $hotelService;
    }

    /**
     * Muestra una lista de todos los hoteles.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $hotels = $this->hotelService->getAll();
        return HotelResource::collection($hotels)->response();
    }

    /**
     * Muestra un hotel específico con su ciudad.
     * CORRECCIÓN: Usa el método del servicio que hace eager loading.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        $hotel = $this->hotelService->getByIdWithCity($id);

        if (!$hotel) {
            return response()->json(['message' => 'Hotel not found'], Response::HTTP_NOT_FOUND);
        }

        return (new HotelResource($hotel))
            ->response()
            ->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Almacena un nuevo hotel.
     *
     * @param StoreHotelRequest $request
     * @return JsonResponse
     */
    public function store(StoreHotelRequest $request): JsonResponse
    {
        try {
            $hotelData = $request->validated();

            // Obtener el ID del usuario autenticado
            $userId = Auth::id();

            // Si el usuario no está autenticado, detén el proceso y responde con un error 401.
            // Esto es una validación de seguridad extra que el middleware debería hacer, pero no sobra.
            if (!$userId) {
                return response()->json(['error' => 'No authenticated user found.'], Response::HTTP_UNAUTHORIZED);
            }

            // Agrega el user_id a los datos del hotel
            $hotelData['user_id'] = $userId;

            // ### PUNTO DE VERIFICACIÓN CRÍTICO ###
            // Loguea los datos que se van a pasar al servicio.
            // Esto te permitirá ver si 'user_id' está presente y tiene un valor válido.
            Log::info('HotelController - Datos a crear:', $hotelData);

            $hotel = $this->hotelService->create($hotelData);

            return (new HotelResource($hotel))
                ->response()
                ->setStatusCode(Response::HTTP_CREATED);
        } catch (Exception $e) {
            Log::error('HotelController - Error al crear hotel: ', ['exception' => $e->getMessage()]);
            return response()->json(['error' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Actualiza un hotel existente.
     * CORRECCIÓN: Maneja explícitamente el caso de hotel no encontrado.
     *
     * @param UpdateHotelRequest $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(UpdateHotelRequest $request, int $id): JsonResponse
    {
        try {
            $updatedData = $request->validated();
            $hotel = $this->hotelService->update($id, $updatedData);

            // Si el servicio devolvió null, significa que no se encontró el hotel.
            if (!$hotel) {
                return response()->json(['message' => 'Hotel not found'], Response::HTTP_NOT_FOUND);
            }

            return (new HotelResource($hotel))
                ->response()
                ->setStatusCode(Response::HTTP_OK);
        } catch (Exception $e) {
            // Este catch es una red de seguridad para otros errores inesperados.
            return response()->json(['error' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Elimina un hotel.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        if ($this->hotelService->delete($id)) {
            return response()->json(null, Response::HTTP_NO_CONTENT);
        }

        return response()->json(['message' => 'Hotel not found or could not be deleted'], Response::HTTP_NOT_FOUND);
    }
}
