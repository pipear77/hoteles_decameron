<?php
// src/app/Repositories/HotelRepository.php

namespace App\Repositories;

use App\Models\Hotel;
use Illuminate\Database\Eloquent\Collection;

/**
 * Repositorio para la entidad Hotel.
 *
 * Esta clase abstrae la lógica de acceso a datos para el modelo Hotel,
 * desacoplando la capa de negocio de la de persistencia.
 */
class HotelRepository implements HotelRepositoryInterface
{
    /**
     * @var Hotel
     */
    protected $model;

    /**
     * @param Hotel $model
     */
    public function __construct(Hotel $model)
    {
        $this->model = $model;
    }

    /**
     * Obtiene todos los hoteles.
     *
     * @return Collection<int, Hotel>
     */
    public function all(): Collection
    {
        return $this->model->all();
    }

    /**
     * Encuentra un hotel por su ID.
     *
     * @param int $id
     * @return Hotel|null
     */
    public function find(int $id): ?Hotel
    {
        return $this->model->find($id);
    }

    /**
     * Busca hoteles por su nombre (utiliza un 'like').
     *
     * Renombrado de 'findByName' a 'searchByName' para ser más explícito
     * sobre el comportamiento de búsqueda con 'like'.
     *
     * @param string $name
     * @return Collection<int, Hotel>
     */
    public function searchByName(string $name): Collection
    {
        // Centraliza la lógica de búsqueda aquí.
        return $this->model->where('name', 'like', "%{$name}%")->get();
    }

    /**
     * Recupera un hotel por su ID, con la relación de la ciudad cargada.
     *
     * @param int $id
     * @return Hotel|null
     */
    public function getHotelByIdWithCity(int $id): ?Hotel
    {
        return $this->model->with('city')->find($id);
    }

    /**
     * Crea un nuevo hotel con los datos proporcionados.
     *
     * @param array $data
     * @return Hotel
     */
    public function create(array $data): Hotel
    {
        return $this->model->create($data);
    }

    /**
     * Actualiza un hotel existente.
     *
     * @param int $id El ID del hotel a actualizar.
     * @param array $data Los datos a actualizar.
     * @return Hotel|null El hotel actualizado o null si no se encuentra.
     */
    public function update(int $id, array $data): ?Hotel
    {
        $hotel = $this->find($id);
        if ($hotel) {
            $hotel->update($data);
            return $hotel;
        }

        return null;
    }

    /**
     * Elimina un hotel de la base de datos.
     *
     * @param int $id El ID del hotel a eliminar.
     * @return bool
     */
    public function delete(int $id): bool
    {
        $hotel = $this->find($id);
        return $hotel ? $hotel->delete() : false;
    }
}
