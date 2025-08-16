<?php

namespace App\Policies;

use App\Models\Hotel;
use App\Models\HotelRoomConfiguration;
use Illuminate\Auth\Access\Response;

class HotelRoomConfigurationPolicy
{
    /**
     * Determine whether the authenticated request can view any models.
     *
     * @param object $user
     * @param Hotel $hotel
     * @return bool
     */
    public function viewAny(object $user, Hotel $hotel): bool
    {
        // Regla: Cualquier usuario autenticado puede ver las configuraciones.
        // Se puede añadir lógica más compleja si fuera necesario, como un rol.
        return true;
    }

    /**
     * Determine whether the authenticated request can view the model.
     *
     * @param object $user
     * @param HotelRoomConfiguration $hotelRoomConfiguration
     * @return bool
     */
    public function view(object $user, HotelRoomConfiguration $hotelRoomConfiguration): bool
    {
        return true;
    }

    /**
     * Determine whether the authenticated request can create models.
     *
     * @param object $user
     * @param Hotel $hotel
     * @return bool
     */
    public function create(object $user, Hotel $hotel): bool
    {
        // Lógica de autorización para crear configuraciones.
        // Por ejemplo, que solo un usuario con un rol específico pueda crearla.
        return true;
    }

    /**
     * Determine whether the authenticated request can update the model.
     *
     * @param object $user
     * @param HotelRoomConfiguration $hotelRoomConfiguration
     * @return bool
     */
    public function update(object $user, HotelRoomConfiguration $hotelRoomConfiguration): bool
    {
        // La validación de la ruta ya garantiza que el usuario ha solicitado un hotel
        // específico. La policy se enfoca en si puede editar la configuración.
        return true;
    }

    /**
     * Determine whether the authenticated request can delete the model.
     *
     * @param object $user
     * @param HotelRoomConfiguration $hotelRoomConfiguration
     * @return bool
     */
    public function delete(object $user, HotelRoomConfiguration $hotelRoomConfiguration): bool
    {
        // La misma lógica de autorización para eliminar.
        return true;
    }
}
