<?php
// src/tests/Unit/HotelServiceTest.php

namespace Tests\Unit;

use App\Models\Hotel;
use App\Models\HotelRoomConfiguration;
use App\Repositories\HotelRepositoryInterface;
use App\Repositories\HotelRoomConfigurationRepositoryInterface;
use App\Services\HotelService;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Validation\ValidationException;
use Mockery;
use Tests\TestCase;

/**
 * Pruebas unitarias para la clase HotelService.
 *
 * Estas pruebas verifican que la lógica del servicio delega correctamente
 * las responsabilidades a los repositorios y maneja la lógica de negocio,
 * incluyendo las transacciones, de forma atómica y coherente.
 */
class HotelServiceTest extends TestCase
{
    /**
     * @var HotelRepositoryInterface|Mockery\MockInterface
     */
    protected $hotelRepository;

    /**
     * @var HotelRoomConfigurationRepositoryInterface|Mockery\MockInterface
     */
    protected $roomConfigurationRepository;

    /**
     * @var HotelService
     */
    protected $hotelService;

    public function setUp(): void
    {
        parent::setUp();
        Mockery::close();

        // Crear los mocks para las dependencias.
        $this->hotelRepository = Mockery::mock(HotelRepositoryInterface::class);
        $this->roomConfigurationRepository = Mockery::mock(HotelRoomConfigurationRepositoryInterface::class);

        // Instanciar el servicio con los mocks.
        $this->hotelService = new HotelService(
            $this->hotelRepository,
            $this->roomConfigurationRepository
        );
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    /**
     * Prueba que 'getAll' delega al repositorio de hoteles.
     *
     * @test
     */
    public function it_delegates_get_all_to_hotel_repository(): void
    {
        $this->hotelRepository->shouldReceive('searchByName')
            ->once()
            ->with('') // CORRECCIÓN: El mock ahora espera una cadena vacía en lugar de null
            ->andReturn(new Collection([]));

        $result = $this->hotelService->getAll();

        $this->assertInstanceOf(Collection::class, $result);
    }

    /**
     * Prueba que 'getByIdWithCity' delega al repositorio de hoteles.
     *
     * @test
     */
    public function it_delegates_get_by_id_with_city_to_hotel_repository(): void
    {
        $hotel = Hotel::factory()->make(['id' => 1]);
        $this->hotelRepository->shouldReceive('getHotelByIdWithCity')
            ->once()
            ->with(1)
            ->andReturn($hotel);

        $result = $this->hotelService->getByIdWithCity(1);

        $this->assertEquals($hotel, $result);
    }

    /**
     * Prueba que 'create' delega a ambos repositorios y maneja la transacción.
     *
     * @test
     */
    public function it_creates_a_new_hotel_and_its_room_configurations(): void
    {
        $hotelData = [
            'name' => 'Nuevo Hotel',
            'address' => '123 Main St',
            'nit' => '1234567890',
            'rooms_total' => 10,
            'email' => 'contact@newhotel.com',
            'city_id' => 1,
            'room_configurations' => [
                ['room_type' => 'Standard', 'quantity' => 10]
            ]
        ];
        $createdHotel = Hotel::factory()->make(['id' => 1]);

        $this->hotelRepository->shouldReceive('create')
            ->once()
            ->with(Mockery::subset(collect($hotelData)->except('room_configurations')->all()))
            ->andReturn($createdHotel);

        $this->roomConfigurationRepository->shouldReceive('create')
            ->once()
            ->with(Mockery::subset([
                'hotel_id' => $createdHotel->id,
                'room_type' => 'Standard',
                'quantity' => 10
            ]));

        $result = $this->hotelService->create($hotelData);

        $this->assertEquals($createdHotel, $result);
    }

    /**
     * Prueba que 'update' delega a ambos repositorios y maneja la transacción.
     *
     * @test
     */
    public function it_updates_an_existing_hotel_and_its_room_configurations(): void
    {
        $hotel = Hotel::factory()->make(['id' => 1]);
        $updatedData = [
            'name' => 'Hotel Actualizado',
            'address' => '456 Oak St',
            'nit' => '1234567890',
            'rooms_total' => 5,
            'email' => 'contact@updatedhotel.com',
            'city_id' => 1,
            'room_configurations' => [
                ['room_type' => 'Suite', 'quantity' => 5]
            ]
        ];

        $this->hotelRepository->shouldReceive('find')
            ->once()
            ->with(1)
            ->andReturn($hotel);

        $this->hotelRepository->shouldReceive('update')
            ->once()
            ->with(1, Mockery::subset(collect($updatedData)->except('room_configurations')->all()))
            ->andReturn($hotel);

        // Se simula la eliminación y creación de las configuraciones de habitaciones.
        $this->roomConfigurationRepository->shouldReceive('deleteByHotelId')
            ->once()
            ->with(1);

        $this->roomConfigurationRepository->shouldReceive('create')
            ->once()
            ->with(Mockery::subset([
                'hotel_id' => 1,
                'room_type' => 'Suite',
                'quantity' => 5
            ]));

        $result = $this->hotelService->update(1, $updatedData);

        $this->assertEquals($hotel, $result);
    }

    /**
     * Prueba que 'update' devuelve null si el hotel no existe.
     *
     * @test
     */
    public function it_returns_null_when_updating_a_non_existent_hotel(): void
    {
        $this->hotelRepository->shouldReceive('find')
            ->once()
            ->with(999)
            ->andReturn(null);

        $result = $this->hotelService->update(999, ['name' => 'Non Existent Hotel']);

        $this->assertNull($result);
    }

    /**
     * Prueba que 'delete' delega al repositorio de hoteles.
     *
     * @test
     */
    public function it_deletes_a_hotel(): void
    {
        $this->hotelRepository->shouldReceive('delete')
            ->once()
            ->with(1)
            ->andReturn(true);

        $result = $this->hotelService->delete(1);

        $this->assertTrue($result);
    }

    /**
     * Prueba que 'delete' devuelve false si el hotel no existe.
     *
     * @test
     */
    public function it_returns_false_when_deleting_a_non_existent_hotel(): void
    {
        $this->hotelRepository->shouldReceive('delete')
            ->once()
            ->with(999)
            ->andReturn(false);

        $result = $this->hotelService->delete(999);

        $this->assertFalse($result);
    }
}
