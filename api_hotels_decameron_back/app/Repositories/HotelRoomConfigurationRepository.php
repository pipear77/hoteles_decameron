<?php

namespace App\Repositories;

use App\Models\HotelRoomConfiguration;
use Illuminate\Database\Eloquent\Collection;

class HotelRoomConfigurationRepository implements HotelRoomConfigurationRepositoryInterface
{
    /**
     * @var HotelRoomConfiguration
     */
    protected HotelRoomConfiguration $model;

    /**
     * @param HotelRoomConfiguration $model
     */
    public function __construct(HotelRoomConfiguration $model)
    {
        $this->model = $model;
    }

    /**
     * Obtiene todas las configuraciones de habitaciones.
     *
     * @return Collection<int, HotelRoomConfiguration>
     */
    public function all(): Collection
    {
        return $this->model->all();
    }

    /**
     * Crea una nueva configuración de habitación.
     *
     * @param array $data
     * @return HotelRoomConfiguration
     */
    public function create(array $data): HotelRoomConfiguration
    {
        return $this->model->create($data);
    }

    /**
     * Busca una configuración de habitación por su ID.
     *
     * @param int $id
     * @return HotelRoomConfiguration|null
     */
    public function find(int $id): ?HotelRoomConfiguration
    {
        return $this->model->find($id);
    }

    /**
     * Actualiza una configuración de habitación existente.
     *
     * Se refactoriza para reutilizar el método `find()`, eliminando la búsqueda duplicada
     * y haciendo el código más limpio y fácil de mantener (DRY).
     *
     * @param int $id
     * @param array $data
     * @return HotelRoomConfiguration|null
     */
    public function update(int $id, array $data): ?HotelRoomConfiguration
    {
        $configuration = $this->find($id);

        if ($configuration) {
            $configuration->update($data);
            return $configuration;
        }

        return null;
    }

    /**
     * Elimina una configuración de habitación.
     *
     * Se refactoriza para reutilizar el método `find()`, mejorando la legibilidad.
     *
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool
    {
        $configuration = $this->find($id);

        return $configuration ? $configuration->delete() : false;
    }

    /**
     * Elimina todas las configuraciones de habitaciones de un hotel dado.
     *
     * @param int $hotelId
     * @return int El número de registros eliminados.
     */
    public function deleteByHotelId(int $hotelId): int
    {
        return $this->model->where('hotel_id', $hotelId)->delete();
    }

    /**
     * Calcula la cantidad total de habitaciones configuradas para un hotel,
     * excluyendo una configuración específica si se proporciona un ID.
     *
     * @param int $hotelId
     * @param int|null $excludeId
     * @return int
     */
    public function getExistingRoomsQuantity(int $hotelId, ?int $excludeId = null): int
    {
        $query = $this->model->where('hotel_id', $hotelId);

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        return $query->sum('quantity');
    }

    /**
     * Retorna las configuraciones para un hotel específico.
     *
     * @param int $hotelId
     * @return Collection<int, HotelRoomConfiguration>
     */
    public function getByHotelId(int $hotelId): Collection
    {
        return $this->model->where('hotel_id', $hotelId)->get();
    }

    /**
     * Retorna la cantidad total de habitaciones para un hotel específico.
     *
     * @param int $hotelId
     * @return int
     */
    public function getTotalRoomQuantityByHotelId(int $hotelId): int
    {
        return $this->model->where('hotel_id', $hotelId)->sum('quantity');
    }
}
