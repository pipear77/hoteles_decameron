<?php

namespace App\Services;

use App\Models\Accommodation;
use App\Models\Hotel;
use App\Models\RoomType;
use App\Repositories\HotelRepositoryInterface;
use App\Repositories\HotelRoomConfigurationRepositoryInterface;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class HotelService implements HotelServiceInterface
{
    /**
     * @var HotelRepositoryInterface
     */
    protected HotelRepositoryInterface $hotelRepository;

    /**
     * @var HotelRoomConfigurationRepositoryInterface
     */
    protected HotelRoomConfigurationRepositoryInterface $roomConfigurationRepository;

    public function __construct(
        HotelRepositoryInterface $hotelRepository,
        HotelRoomConfigurationRepositoryInterface $roomConfigurationRepository
    ) {
        $this->hotelRepository = $hotelRepository;
        $this->roomConfigurationRepository = $roomConfigurationRepository;
    }

    /**
     * Obtiene todos los hoteles, opcionalmente filtrados por nombre.
     *
     * @param string|null $name
     * @return Collection
     */
    public function getAll(?string $name = null): Collection
    {
        // Se pasa una cadena vacía en lugar de null al repositorio.
        return $this->hotelRepository->searchByName($name ?? '');
    }

    /**
     * Obtiene un hotel por ID, con la relación de la ciudad.
     *
     * @param int $id
     * @return Hotel|null
     */
    public function getByIdWithCity(int $id): ?Hotel
    {
        return $this->hotelRepository->getHotelByIdWithCity($id);
    }

    /**
     * Crea un nuevo hotel y sus configuraciones de habitaciones asociadas.
     *
     * @param array $data Los datos del hotel, incluyendo las configuraciones de habitaciones.
     * @return Hotel
     * @throws Exception
     */
    public function create(array $data): Hotel
    {
        return DB::transaction(function () use ($data) {

            $roomConfigurations = $data['room_configurations'] ?? [];
            unset($data['room_configurations']);

            $hotel = Hotel::create($data);

            foreach ($roomConfigurations as $config) {

                // Validación para asegurarnos de que la clave 'accommodation' existe en el JSON de entrada.
                if (!isset($config['accommodation'])) {
                    throw new Exception("Missing required key 'accommodation' in a room configuration.");
                }

                $roomType = RoomType::where('name', $config['room_type'])->first();
                $accommodation = Accommodation::where('name', $config['accommodation'])->first();

                if (!$roomType) {
                    throw new Exception("Room type '{$config['room_type']}' not found.");
                }

                if (!$accommodation) {
                    throw new Exception("Accommodation type '{$config['accommodation']}' not found.");
                }

                $hotel->roomConfigurations()->create([
                    'room_type_id' => $roomType->id,
                    'accommodation_id' => $accommodation->id,
                    'quantity' => $config['quantity'],
                ]);
            }

            return $hotel;
        });
    }

    /**
     * Actualiza un hotel existente y sus configuraciones de habitaciones.
     *
     * @param int $id
     * @param array $data
     * @return Hotel|null
     * @throws Exception
     */
    public function update(int $id, array $data): ?Hotel
    {
        \Log::info('UpdateHotelRequest - Data recibida en service:', $data);
        return DB::transaction(function () use ($id, $data) {
            $hotel = Hotel::find($id);

            if (!$hotel) {
                return null;
            }

            // Actualizamos los datos del hotel
            $hotel->update($data);

            // Si vienen configuraciones de habitaciones, las reemplazamos
            if (isset($data['room_configurations'])) {
                // Primero borramos las existentes (si quieres reemplazar)
                $hotel->roomConfigurations()->delete();

                // Creamos las nuevas
                $hotel->roomConfigurations()->createMany(
                    collect($data['room_configurations'])->map(function ($config) {
                        return [
                            'room_type_id'     => $config['room_type_id'],
                            'accommodation_id' => $config['accommodation_id'],
                            'quantity'         => $config['quantity'],
                        ];
                    })->toArray()
                );
            }

            return $hotel->fresh(['roomConfigurations']);
        });
    }

    /**
     * Elimina un hotel y sus configuraciones de habitaciones asociadas.
     *
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool
    {
        return $this->hotelRepository->delete($id);
    }

    /**
     * Crea las configuraciones de habitaciones.
     *
     * @param int $hotelId
     * @param array $configurations
     * @return void
     */
    private function createRoomConfigurations(int $hotelId, array $configurations): void
    {
        foreach ($configurations as $config) {
            $this->roomConfigurationRepository->create([
                'hotel_id' => $hotelId,
                'room_type' => $config['room_type'],
                'quantity' => $config['quantity'],
            ]);
        }
    }
}
