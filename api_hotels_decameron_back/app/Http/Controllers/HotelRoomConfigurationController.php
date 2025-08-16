<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreHotelRoomConfigurationRequest;
use App\Http\Requests\UpdateHotelRoomConfigurationRequest;
use App\Models\Hotel;
use App\Models\HotelRoomConfiguration;
use App\Services\HotelRoomConfigurationServiceInterface;
use Illuminate\Http\JsonResponse;

/**
 * Clase controladora para gestionar las configuraciones de habitaciones de un hotel.
 *
 * Sigue el principio SOLID de Responsabilidad Única (SRP) delegando la lógica de negocio
 * a la capa de servicio (Service). El controlador solo maneja las peticiones HTTP
 * y las respuestas.
 */
class HotelRoomConfigurationController extends Controller
{
    /**
     * @var HotelRoomConfigurationServiceInterface
     */
    protected HotelRoomConfigurationServiceInterface $hotelRoomConfigurationService;

    /**
     * Constructor del controlador.
     *
     * @param HotelRoomConfigurationServiceInterface $hotelRoomConfigurationService
     */
    public function __construct(HotelRoomConfigurationServiceInterface $hotelRoomConfigurationService)
    {
        $this->hotelRoomConfigurationService = $hotelRoomConfigurationService;
    }

    /**
     * Muestra una lista de configuraciones de habitaciones para un hotel específico.
     *
     * @param Hotel $hotel
     * @return JsonResponse
     */
    public function index(Hotel $hotel): JsonResponse
    {
        $configurations = $this->hotelRoomConfigurationService->getByHotelId($hotel->id);

        return response()->json($configurations);
    }

    /**
     * Almacena una nueva configuración de habitación para un hotel.
     *
     * @param StoreHotelRoomConfigurationRequest $request
     * @param Hotel $hotel
     * @return JsonResponse
     */
    public function store(StoreHotelRoomConfigurationRequest $request, Hotel $hotel): JsonResponse
    {
        // La validación y autorización se manejan automáticamente por el Form Request.
        $newConfiguration = $this->hotelRoomConfigurationService->create(
            $hotel->id,
            $request->validated()
        );

        return response()->json($newConfiguration, 201);
    }

    /**
     * Muestra una configuración de habitación específica para un hotel.
     *
     * @param Hotel $hotel
     * @param HotelRoomConfiguration $hotelRoomConfiguration
     * @return JsonResponse
     */
    public function show(Hotel $hotel, HotelRoomConfiguration $hotelRoomConfiguration): JsonResponse
    {
        // La validación de pertenencia se maneja automáticamente por la ligadura de modelos
        // y la política de autorización, por lo que no es necesario el chequeo manual.
        return response()->json($hotelRoomConfiguration);
    }

    /**
     * Actualiza una configuración de habitación existente.
     *
     * @param UpdateHotelRoomConfigurationRequest $request
     * @param Hotel $hotel
     * @param HotelRoomConfiguration $hotelRoomConfiguration
     * @return JsonResponse
     */
    public function update(UpdateHotelRoomConfigurationRequest $request, Hotel $hotel, HotelRoomConfiguration $hotelRoomConfiguration): JsonResponse
    {
        // Se valida que la configuración pertenezca al hotel del que se hace la petición
        // para prevenir manipulaciones maliciosas de la URL.
        if ($hotelRoomConfiguration->hotel_id !== $hotel->id) {
            abort(404, 'Hotel room configuration not found or does not belong to this hotel.');
        }

        $updatedConfiguration = $this->hotelRoomConfigurationService->update(
            $hotelRoomConfiguration->id,
            $request->validated()
        );

        return response()->json($updatedConfiguration);
    }

    /**
     * Elimina una configuración de habitación.
     *
     * @param Hotel $hotel
     * @param HotelRoomConfiguration $hotelRoomConfiguration
     * @return JsonResponse
     */
    public function destroy(Hotel $hotel, HotelRoomConfiguration $hotelRoomConfiguration): JsonResponse
    {
        // Se valida que la configuración pertenezca al hotel del que se hace la petición
        if ($hotelRoomConfiguration->hotel_id !== $hotel->id) {
            abort(404, 'Hotel room configuration not found or does not belong to this hotel.');
        }

        $success = $this->hotelRoomConfigurationService->delete($hotelRoomConfiguration->id);

        if (!$success) {
            abort(500, 'Hotel room configuration could not be deleted.');
        }

        return response()->json(null, 204);
    }
}
