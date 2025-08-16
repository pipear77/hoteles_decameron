<?php

namespace App\Http\Controllers;

use App\Models\Hotel;
use App\Models\HotelRoomConfiguration;
use App\Http\Requests\StoreHotelRoomConfigurationRequest;
use App\Http\Requests\UpdateHotelRoomConfigurationRequest;

class HotelRoomConfigurationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param \App\Models\Hotel $hotel
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Hotel $hotel)
    {
        // VERIFICACIÓN DE PERMISOS: Solo el dueño del hotel puede ver sus configuraciones.
        if (auth()->user()->id !== $hotel->user_id) {
            abort(403, 'No tienes permiso para ver las configuraciones de este hotel.');
        }

        return response()->json($hotel->roomConfigurations()->get());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \App\Http\Requests\StoreHotelRoomConfigurationRequest $request
     * @param \App\Models\Hotel $hotel
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreHotelRoomConfigurationRequest $request, Hotel $hotel)
    {
        // VERIFICACIÓN DE PERMISOS: Solo el dueño del hotel puede crear configuraciones.
        if (auth()->user()->id !== $hotel->user_id) {
            abort(403, 'No tienes permiso para crear configuraciones en este hotel.');
        }

        // Obtener la cantidad total de habitaciones ya configuradas para este hotel.
        $configuredRooms = $hotel->roomConfigurations()->sum('quantity');

        // Validar que la nueva cantidad no exceda la capacidad total del hotel.
        if (($configuredRooms + $request->quantity) > $hotel->rooms_total) {
            return response()->json([
                'message' => 'La cantidad de habitaciones excede la capacidad total del hotel.',
                'errors' => ['quantity' => ['La cantidad de habitaciones excede la capacidad total del hotel.']],
            ], 422);
        }

        $configuration = $hotel->roomConfigurations()->create($request->validated());

        return response()->json($configuration, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\Hotel $hotel
     * @param \App\Models\HotelRoomConfiguration $roomConfiguration
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Hotel $hotel, HotelRoomConfiguration $roomConfiguration)
    {
        // VERIFICACIÓN DE PERMISOS: Solo el dueño del hotel puede ver una configuración específica.
        if (auth()->user()->id !== $hotel->user_id) {
            abort(403, 'No tienes permiso para ver esta configuración.');
        }

        // VERIFICACIÓN DE RELACIÓN: Se asegura de que la configuración pertenece al hotel.
        if ($roomConfiguration->hotel_id !== $hotel->id) {
            abort(404, 'La configuración no pertenece a este hotel.');
        }

        return response()->json($roomConfiguration);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \App\Http\Requests\UpdateHotelRoomConfigurationRequest $request
     * @param \App\Models\Hotel $hotel
     * @param \App\Models\HotelRoomConfiguration $roomConfiguration
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateHotelRoomConfigurationRequest $request, Hotel $hotel, HotelRoomConfiguration $roomConfiguration)
    {
        // VERIFICACIÓN DE PERMISOS: Solo el dueño del hotel puede actualizar una configuración.
        if (auth()->user()->id !== $hotel->user_id) {
            abort(403, 'No tienes permiso para actualizar esta configuración.');
        }

        // VERIFICACIÓN DE RELACIÓN: Se asegura de que la configuración pertenece al hotel.
        if ($roomConfiguration->hotel_id !== $hotel->id) {
            abort(404, 'La configuración no pertenece a este hotel.');
        }

        $configuration = $request->validated();
        $roomConfiguration->update($configuration);

        return response()->json($roomConfiguration);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Hotel $hotel
     * @param \App\Models\HotelRoomConfiguration $roomConfiguration
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Hotel $hotel, HotelRoomConfiguration $roomConfiguration)
    {
        // VERIFICACIÓN DE PERMISOS: Solo el dueño del hotel puede eliminar una configuración.
        if (auth()->user()->id !== $hotel->user_id) {
            abort(403, 'No tienes permiso para eliminar esta configuración.');
        }

        // VERIFICACIÓN DE RELACIÓN: Se asegura de que la configuración pertenece al hotel.
        if ($roomConfiguration->hotel_id !== $hotel->id) {
            abort(404, 'La configuración no pertenece a este hotel.');
        }

        $roomConfiguration->delete();

        return response()->json(null, 204);
    }
}
