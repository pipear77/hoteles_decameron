<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\RoomType;
use App\Services\RoomTypeService;
use App\Repositories\RoomTypeRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Mockery;

/**
 * Pruebas Unitarias para la clase RoomTypeService.
 *
 * Estas pruebas garantizan que el servicio delega correctamente las
 * operaciones CRUD a su repositorio subyacente.
 */
class RoomTypeServiceTest extends TestCase
{
    /**
     * @var Mockery\MockInterface|RoomTypeRepositoryInterface
     */
    protected $roomTypeRepositoryMock;

    /**
     * @var RoomTypeService
     */
    protected $roomTypeService;

    /**
     * Configuración inicial para cada prueba.
     * Se crea un mock del repositorio y se inyecta en el servicio.
     */
    public function setUp(): void
    {
        parent::setUp();
        // Se crea un mock del repositorio para simular su comportamiento
        // sin depender de una base de datos real.
        $this->roomTypeRepositoryMock = Mockery::mock(RoomTypeRepositoryInterface::class);

        // Se inicializa el servicio, inyectando el mock del repositorio.
        $this->roomTypeService = new RoomTypeService($this->roomTypeRepositoryMock);
    }

    /**
     * Limpia los mocks de Mockery después de cada prueba.
     */
    public function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    /**
     * Prueba que el método getAll llama al método 'all' del repositorio.
     */
    public function test_it_can_get_all_room_types()
    {
        // Arrange: Se define el comportamiento esperado del mock.
        $expectedCollection = new Collection();
        $this->roomTypeRepositoryMock->shouldReceive('all')
            ->once()
            ->andReturn($expectedCollection);

        // Act: Se llama al método del servicio.
        $result = $this->roomTypeService->getAll();

        // Assert: Se verifica que el resultado es una instancia de Collection.
        $this->assertInstanceOf(Collection::class, $result);
        $this->assertEquals($expectedCollection, $result);
    }

    /**
     * Prueba que el método find llama al método 'find' del repositorio.
     */
    public function test_it_can_find_a_room_type_by_id()
    {
        // Arrange: Se define el ID a buscar y el objeto mock que se espera retornar.
        $roomTypeId = 1;
        $expectedRoomType = Mockery::mock(RoomType::class);
        $this->roomTypeRepositoryMock->shouldReceive('find')
            ->once()
            ->with($roomTypeId)
            ->andReturn($expectedRoomType);

        // Act: Se llama al método del servicio.
        $result = $this->roomTypeService->find($roomTypeId);

        // Assert: Se verifica que el resultado es el objeto esperado.
        $this->assertEquals($expectedRoomType, $result);
    }

    /**
     * Prueba que el método create llama al método 'create' del repositorio.
     */
    public function test_it_can_create_a_room_type()
    {
        // Arrange: Se definen los datos a crear y el objeto mock que se espera retornar.
        $data = ['name' => 'Individual', 'description' => 'Habitación para una persona.'];
        $expectedRoomType = Mockery::mock(RoomType::class);
        $this->roomTypeRepositoryMock->shouldReceive('create')
            ->once()
            ->with($data)
            ->andReturn($expectedRoomType);

        // Act: Se llama al método del servicio.
        $result = $this->roomTypeService->create($data);

        // Assert: Se verifica que el resultado es el objeto esperado.
        $this->assertEquals($expectedRoomType, $result);
    }

    /**
     * Prueba que el método update llama al método 'update' del repositorio.
     */
    public function test_it_can_update_a_room_type()
    {
        // Arrange: Se define el ID y los datos a actualizar, y el objeto mock esperado.
        $roomTypeId = 1;
        $data = ['description' => 'Habitación doble con vistas al mar.'];
        $expectedRoomType = Mockery::mock(RoomType::class);
        $this->roomTypeRepositoryMock->shouldReceive('update')
            ->once()
            ->with($roomTypeId, $data)
            ->andReturn($expectedRoomType);

        // Act: Se llama al método del servicio.
        $result = $this->roomTypeService->update($roomTypeId, $data);

        // Assert: Se verifica que el resultado es el objeto esperado.
        $this->assertEquals($expectedRoomType, $result);
    }

    /**
     * Prueba que el método delete llama al método 'delete' del repositorio.
     */
    public function test_it_can_delete_a_room_type()
    {
        // Arrange: Se define el ID a eliminar y el comportamiento esperado.
        $roomTypeId = 1;
        $this->roomTypeRepositoryMock->shouldReceive('delete')
            ->once()
            ->with($roomTypeId)
            ->andReturn(true);

        // Act: Se llama al método del servicio.
        $result = $this->roomTypeService->delete($roomTypeId);

        // Assert: Se verifica que el resultado sea verdadero.
        $this->assertTrue($result);
    }
}
