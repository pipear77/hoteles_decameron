<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Hotel;
use App\Models\HotelRoomConfiguration;
use App\Services\HotelRoomConfigurationService;
use App\Repositories\HotelRoomConfigurationRepositoryInterface;
use App\Repositories\HotelRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
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
    protected $repositoryMock;

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
        $this->repositoryMock = Mockery::mock(HotelRoomConfigurationRepositoryInterface::class);
        $this->hotelRepositoryMock = Mockery::mock(HotelRepositoryInterface::class);
        $this->service = new HotelRoomConfigurationService(
            $this->repositoryMock,
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

    // --- Pruebas de delegación simples ---

    /**
     * Prueba que getAll delega correctamente al repositorio.
     */
    public function test_it_can_get_all_configurations()
    {
        // Arrange
        $expectedCollection = new Collection();
        $this->repositoryMock->shouldReceive('all')
            ->once()
            ->andReturn($expectedCollection);

        // Act
        $result = $this->service->getAll();

        // Assert
        $this->assertEquals($expectedCollection, $result);
    }

    /**
     * Prueba que getByHotelId delega correctamente al repositorio.
     */
    public function test_it_can_get_configurations_by_hotel_id()
    {
        // Arrange
        $hotelId = 1;
        $expectedCollection = new Collection();
        $this->repositoryMock->shouldReceive('getByHotelId')
            ->once()
            ->with($hotelId)
            ->andReturn($expectedCollection);

        // Act
        $result = $this->service->getByHotelId($hotelId);

        // Assert
        $this->assertEquals($expectedCollection, $result);
    }

    /**
     * Prueba que find delega correctamente al repositorio.
     */
    public function test_it_can_find_a_configuration_by_id()
    {
        // Arrange
        $id = 1;
        $expectedConfiguration = Mockery::mock(HotelRoomConfiguration::class);
        $this->repositoryMock->shouldReceive('find')
            ->once()
            ->with($id)
            ->andReturn($expectedConfiguration);

        // Act
        $result = $this->service->find($id);

        // Assert
        $this->assertEquals($expectedConfiguration, $result);
    }

    /**
     * Prueba que delete delega correctamente al repositorio.
     */
    public function test_it_can_delete_a_configuration()
    {
        // Arrange
        $id = 1;
        $this->repositoryMock->shouldReceive('delete')
            ->once()
            ->with($id)
            ->andReturn(true);

        // Act
        $result = $this->service->delete($id);

        // Assert
        $this->assertTrue($result);
    }

    // --- Pruebas de lógica de negocio para el método create ---

    /**
     * Prueba que se puede crear una configuración de habitación con éxito.
     */
    public function test_it_can_create_a_configuration()
    {
        // Arrange
        $hotelId = 1;
        $data = ['quantity' => 10, 'room_type_id' => 1, 'price' => 100];
        $expectedConfiguration = Mockery::mock(HotelRoomConfiguration::class);

        // CORRECTO: Definimos expectativas explícitas para las propiedades que se van a leer.
        $hotel = Mockery::mock(Hotel::class);
        $hotel->shouldReceive('getAttribute')->with('id')->andReturn($hotelId);
        $hotel->shouldReceive('getAttribute')->with('rooms_total')->andReturn(50);

        // Se simula la búsqueda del hotel y la cantidad total de habitaciones existentes
        $this->hotelRepositoryMock->shouldReceive('find')
            ->once()
            ->with($hotelId)
            ->andReturn($hotel);

        $this->repositoryMock->shouldReceive('getTotalRoomQuantityByHotelId')
            ->once()
            ->with($hotelId)
            ->andReturn(20); // 20 habitaciones ya configuradas

        // Se espera que el repositorio de configuración sea llamado con los datos correctos
        $this->repositoryMock->shouldReceive('create')
            ->once()
            ->with(array_merge($data, ['hotel_id' => $hotelId]))
            ->andReturn($expectedConfiguration);

        // Act
        $result = $this->service->create($hotelId, $data);

        // Assert
        $this->assertEquals($expectedConfiguration, $result);
    }

    /**
     * Prueba que el método create lanza una excepción si el hotel no existe.
     */
    public function test_create_throws_exception_if_hotel_does_not_exist()
    {
        // Arrange
        $hotelId = 999;
        $data = ['quantity' => 10, 'room_type_id' => 1, 'price' => 100];

        // Se simula que el repositorio de hoteles no encuentra el hotel
        $this->hotelRepositoryMock->shouldReceive('find')
            ->once()
            ->with($hotelId)
            ->andReturn(null);

        // Assert: Se espera una excepción de validación
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('El hotel especificado no existe.');

        // Act
        $this->service->create($hotelId, $data);
    }

    /**
     * Prueba que el método create lanza una excepción si la cantidad total de habitaciones excede el límite.
     */
    public function test_create_throws_exception_if_total_rooms_exceeds_limit()
    {
        // Arrange
        $hotelId = 1;
        $data = ['quantity' => 31, 'room_type_id' => 1, 'price' => 100];

        // CORRECTO: Se definen expectativas explícitas para las propiedades.
        $hotel = Mockery::mock(Hotel::class);
        $hotel->shouldReceive('getAttribute')->with('id')->andReturn($hotelId);
        $hotel->shouldReceive('getAttribute')->with('rooms_total')->andReturn(50);

        // Se simula que ya hay 20 habitaciones configuradas
        $this->hotelRepositoryMock->shouldReceive('find')
            ->once()
            ->with($hotelId)
            ->andReturn($hotel);
        $this->repositoryMock->shouldReceive('getTotalRoomQuantityByHotelId')
            ->once()
            ->with($hotelId)
            ->andReturn(20);

        // Assert: Se espera una excepción de validación
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('La cantidad de habitaciones supera el máximo (50) para este hotel.');

        // Act
        $this->service->create($hotelId, $data);
    }


    // --- Pruebas de lógica de negocio para el método update ---

    /**
     * Prueba que se puede actualizar una configuración con éxito.
     */
    public function test_it_can_update_a_configuration()
    {
        // Arrange
        $id = 1;
        $data = ['quantity' => 20];
        $originalQuantity = 15;
        $existingRooms = 30; // habitaciones ya existentes, incluyendo la original

        // CORRECTO: Se definen expectativas explícitas para las propiedades de ambos mocks.
        $hotelConfiguration = Mockery::mock(HotelRoomConfiguration::class);
        $hotelConfiguration->shouldReceive('getAttribute')->with('quantity')->andReturn($originalQuantity);
        $hotelConfiguration->shouldReceive('getAttribute')->with('hotel_id')->andReturn(1);

        $hotel = Mockery::mock(Hotel::class);
        $hotel->shouldReceive('getAttribute')->with('id')->andReturn(1);
        $hotel->shouldReceive('getAttribute')->with('rooms_total')->andReturn(50);

        // Se simulan los retornos de los repositorios
        $this->repositoryMock->shouldReceive('find')
            ->once()
            ->with($id)
            ->andReturn($hotelConfiguration);
        $this->hotelRepositoryMock->shouldReceive('find')
            ->once()
            ->with($hotelConfiguration->hotel_id)
            ->andReturn($hotel);
        $this->repositoryMock->shouldReceive('getTotalRoomQuantityByHotelId')
            ->once()
            ->with($hotelConfiguration->hotel_id)
            ->andReturn($existingRooms);
        $this->repositoryMock->shouldReceive('update')
            ->once()
            ->with($id, $data)
            ->andReturn($hotelConfiguration);

        // Act
        $result = $this->service->update($id, $data);

        // Assert
        $this->assertEquals($hotelConfiguration, $result);
    }

    /**
     * Prueba que el método update no actualiza si la configuración no existe.
     */
    public function test_update_returns_null_if_configuration_does_not_exist()
    {
        // Arrange
        $id = 999;
        $data = ['quantity' => 20];

        // Se simula que el repositorio no encuentra la configuración
        $this->repositoryMock->shouldReceive('find')
            ->once()
            ->with($id)
            ->andReturn(null);

        // Act
        $result = $this->service->update($id, $data);

        // Assert
        $this->assertNull($result);
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
        $existingRooms = 30;

        // CORRECTO: Se definen expectativas explícitas para las propiedades de ambos mocks.
        $hotelConfiguration = Mockery::mock(HotelRoomConfiguration::class);
        $hotelConfiguration->shouldReceive('getAttribute')->with('quantity')->andReturn($originalQuantity);
        $hotelConfiguration->shouldReceive('getAttribute')->with('hotel_id')->andReturn(1);

        $hotel = Mockery::mock(Hotel::class);
        $hotel->shouldReceive('getAttribute')->with('id')->andReturn(1);
        $hotel->shouldReceive('getAttribute')->with('rooms_total')->andReturn(50);

        // Se simulan los retornos de los repositorios
        $this->repositoryMock->shouldReceive('find')
            ->once()
            ->with($id)
            ->andReturn($hotelConfiguration);
        $this->hotelRepositoryMock->shouldReceive('find')
            ->once()
            ->with($hotelConfiguration->hotel_id)
            ->andReturn($hotel);
        $this->repositoryMock->shouldReceive('getTotalRoomQuantityByHotelId')
            ->once()
            ->with($hotelConfiguration->hotel_id)
            ->andReturn($existingRooms);

        // Assert: Se espera una excepción de validación
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('La cantidad de habitaciones supera el máximo (50) para este hotel.');

        // Act
        $this->service->update($id, $data);
    }
}
