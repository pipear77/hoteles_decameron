<?php
// src/tests/Unit/HotelRoomConfigurationServiceTest.php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Hotel;
use App\Models\HotelRoomConfiguration;
use App\Services\HotelRoomConfigurationService;
use App\Repositories\HotelRoomConfigurationRepositoryInterface;
use App\Repositories\HotelRepositoryInterface;
use Illuminate\Validation\ValidationException;
use Mockery;

/**
 * Pruebas Unitarias para HotelRoomConfigurationService.
 *
 * Estas pruebas verifican la lógica de negocio y la correcta
 * delegación de operaciones de persistencia.
 */
class HotelRoomConfigurationServiceTest extends TestCase
{
    /**
     * @var Mockery\MockInterface|HotelRoomConfigurationRepositoryInterface
     */
    protected $roomConfigurationRepositoryMock;

    /**
     * @var Mockery\MockInterface|HotelRepositoryInterface
     */
    protected $hotelRepositoryMock;

    /**
     * @var HotelRoomConfigurationService
     */
    protected $service;

    /**
     * Configuración inicial para cada prueba.
     * Se crean mocks de ambos repositorios y se inyectan en el servicio.
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->roomConfigurationRepositoryMock = Mockery::mock(HotelRoomConfigurationRepositoryInterface::class);
        $this->hotelRepositoryMock = Mockery::mock(HotelRepositoryInterface::class);
        $this->service = new HotelRoomConfigurationService(
            $this->roomConfigurationRepositoryMock,
            $this->hotelRepositoryMock
        );
    }

    /**
     * Limpia los mocks después de cada prueba.
     */
    public function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    /**
     * Prueba que se puede crear una configuración de habitación con éxito.
     */
    public function test_it_can_create_a_configuration()
    {
        // Arrange
        $hotelId = 1;
        $data = ['quantity' => 10, 'room_type_id' => 1, 'price' => 100];
        $expectedConfiguration = new HotelRoomConfiguration($data);

        // Mockeamos el comportamiento de acceso a la propiedad 'rooms_total'.
        $hotel = Mockery::mock(Hotel::class);
        $hotel->shouldReceive('getAttribute')->with('rooms_total')->andReturn(50);
        $hotel->shouldReceive('getAttribute')->with('id')->andReturn($hotelId); // Añadimos la expectativa para el ID.

        $this->hotelRepositoryMock->shouldReceive('find')
            ->once()
            ->with($hotelId)
            ->andReturn($hotel);

        $this->roomConfigurationRepositoryMock->shouldReceive('getTotalRoomQuantityByHotelId')
            ->once()
            ->with($hotelId)
            ->andReturn(20);

        $this->roomConfigurationRepositoryMock->shouldReceive('create')
            ->once()
            ->with(array_merge($data, ['hotel_id' => $hotelId]))
            ->andReturn($expectedConfiguration);

        // Act
        $result = $this->service->create($hotelId, $data);

        // Assert
        $this->assertEquals($expectedConfiguration, $result);
        $this->assertInstanceOf(HotelRoomConfiguration::class, $result);
    }

    /**
     * Prueba que el método create lanza una excepción si la cantidad total de habitaciones excede el límite.
     */
    public function test_create_throws_exception_if_total_rooms_exceeds_limit()
    {
        // Arrange
        $hotelId = 1;
        $data = ['quantity' => 31, 'room_type_id' => 1, 'price' => 100];

        // Usamos shouldReceive para el mock del hotel.
        $hotel = Mockery::mock(Hotel::class);
        $hotel->shouldReceive('getAttribute')->with('rooms_total')->andReturn(50);
        $hotel->shouldReceive('getAttribute')->with('id')->andReturn($hotelId); // Añadimos la expectativa para el ID.

        $this->hotelRepositoryMock->shouldReceive('find')
            ->once()
            ->with($hotelId)
            ->andReturn($hotel);

        $this->roomConfigurationRepositoryMock->shouldReceive('getTotalRoomQuantityByHotelId')
            ->once()
            ->with($hotelId)
            ->andReturn(20);

        // Assert: Se espera una excepción de validación
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('The total quantity of rooms exceeds the hotel\'s capacity of 50.');

        // Act
        $this->service->create($hotelId, $data);
    }

    /**
     * Prueba que se puede actualizar una configuración con éxito.
     */
    public function test_it_can_update_a_configuration()
    {
        // Arrange
        $id = 1;
        $data = ['quantity' => 20];
        $originalQuantity = 15;
        $hotelId = 1;
        $roomsBeforeUpdate = 30; // 30 habitaciones ya configuradas

        // Mocks con shouldReceive para todas las propiedades.
        $hotel = Mockery::mock(Hotel::class);
        $hotel->shouldReceive('getAttribute')->with('rooms_total')->andReturn(50);
        $hotel->shouldReceive('getAttribute')->with('id')->andReturn($hotelId); // Añadimos la expectativa para el ID.

        $configuration = Mockery::mock(HotelRoomConfiguration::class);
        $configuration->shouldReceive('getAttribute')->with('quantity')->andReturn($originalQuantity);
        $configuration->shouldReceive('getAttribute')->with('hotel_id')->andReturn($hotelId);

        $this->roomConfigurationRepositoryMock->shouldReceive('find')
            ->once()
            ->with($id)
            ->andReturn($configuration);

        $this->hotelRepositoryMock->shouldReceive('find')
            ->once()
            ->with($hotelId)
            ->andReturn($hotel);

        $this->roomConfigurationRepositoryMock->shouldReceive('getTotalRoomQuantityByHotelId')
            ->once()
            ->with($hotelId)
            ->andReturn($roomsBeforeUpdate);

        $this->roomConfigurationRepositoryMock->shouldReceive('update')
            ->once()
            ->with($id, $data);

        $this->roomConfigurationRepositoryMock->shouldReceive('find')
            ->once()
            ->with($id)
            ->andReturn($configuration);

        // Act
        $result = $this->service->update($id, $data);

        // Assert
        $this->assertEquals($configuration, $result);
    }

    /**
     * Prueba que el método update lanza una excepción si la cantidad total de habitaciones excede el límite.
     */
    public function test_update_throws_exception_if_total_rooms_exceeds_limit()
    {
        // Arrange
        $id = 1;
        $data = ['quantity' => 36];
        $originalQuantity = 10;
        $hotelId = 1;
        $roomsBeforeUpdate = 30;

        // Mocks con shouldReceive para todas las propiedades.
        $hotel = Mockery::mock(Hotel::class);
        $hotel->shouldReceive('getAttribute')->with('rooms_total')->andReturn(50);
        $hotel->shouldReceive('getAttribute')->with('id')->andReturn($hotelId); // Añadimos la expectativa para el ID.

        $configuration = Mockery::mock(HotelRoomConfiguration::class);
        $configuration->shouldReceive('getAttribute')->with('quantity')->andReturn($originalQuantity);
        $configuration->shouldReceive('getAttribute')->with('hotel_id')->andReturn($hotelId);

        $this->roomConfigurationRepositoryMock->shouldReceive('find')
            ->once()
            ->with($id)
            ->andReturn($configuration);

        $this->hotelRepositoryMock->shouldReceive('find')
            ->once()
            ->with($hotelId)
            ->andReturn($hotel);

        $this->roomConfigurationRepositoryMock->shouldReceive('getTotalRoomQuantityByHotelId')
            ->once()
            ->with($hotelId)
            ->andReturn($roomsBeforeUpdate);

        // Assert
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('The updated quantity of rooms exceeds the hotel\'s capacity of 50.');

        // Act
        $this->service->update($id, $data);
    }

    /**
     * Prueba que el método update retorna null si la configuración no existe.
     */
    public function test_update_returns_null_if_configuration_does_not_exist()
    {
        // Arrange
        $id = 999;
        $data = ['quantity' => 20];

        $this->roomConfigurationRepositoryMock->shouldReceive('find')
            ->once()
            ->with($id)
            ->andReturn(null);

        // Act
        $result = $this->service->update($id, $data);

        // Assert
        $this->assertNull($result);
    }

    /**
     * Prueba que el método delete delega correctamente al repositorio.
     */
    public function test_it_can_delete_a_configuration()
    {
        // Arrange
        $id = 1;
        $this->roomConfigurationRepositoryMock->shouldReceive('delete')
            ->once()
            ->with($id)
            ->andReturn(true);

        // Act
        $result = $this->service->delete($id);

        // Assert
        $this->assertTrue($result);
    }
}
