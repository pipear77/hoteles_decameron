<?php

namespace App\Services;

use App\Models\Hotel;
use App\Repositories\HotelRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Validation\ValidationException;

class HotelService implements HotelServiceInterface
{
    public function __construct(private HotelRepositoryInterface $repository) {}

    public function getAll(?string $name = null): Collection
    {
        return $this->repository->all($name);
    }

    public function getById(int $id): ?Hotel
    {
        return $this->repository->find($id);
    }

    public function create(array $data): Hotel
    {
        // Validación de lógica de negocio
        if (Hotel::sum('rooms_total') + $data['rooms_total'] > 100) {
            throw ValidationException::withMessages([
                'rooms_total' => 'El límite de habitaciones del sistema ha sido superado (máx. 100).'
            ]);
        }
        return $this->repository->create($data);
    }

    public function update(int $id, array $data): ?Hotel
    {
        return $this->repository->update($id, $data);
    }

    public function delete(int $id): bool
    {
        return $this->repository->delete($id);
    }
}
