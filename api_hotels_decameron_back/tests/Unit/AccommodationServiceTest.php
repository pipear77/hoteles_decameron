<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Accommodation;
use App\Services\AccommodationService;
use App\Repositories\AccommodationRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Mockery;

/**
 * Pruebas Unitarias para la clase AccommodationService.
 *
 * Estas pruebas verifican que el servicio interactúa correctamente
 * con su repositorio subyacente, delegando las llamadas de manera apropiada.
 */
class AccommodationServiceTest extends TestCase
{
    /**
     * @var Mockery\MockInterface|AccommodationRepositoryInterface
     */
    protected $accommodationRepositoryMock;

    /**
     * @var AccommodationService
     */
    protected $accommodationService;

    /**
     * Configuración inicial para cada prueba.
     * Se crea un mock del repositorio y se inyecta en el servicio.
     */
    public function setUp(): void
    {
        parent::setUp();
        // Se crea un mock del repositorio para simular su comportamiento.
        $this->accommodationRepositoryMock = Mockery::mock(AccommodationRepositoryInterface::class);

        // Se inicializa el servicio, inyectando el mock como dependencia.
        $this->accommodationService = new AccommodationService($this->accommodationRepositoryMock);
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
     * Prueba que el método getAll llama al método 'all' del repositorio.
     * El servicio no añade lógica, simplemente delega.
     */
    public function test_it_can_get_all_accommodations()
    {
        // Arrange: Se define el comportamiento esperado del mock.
        $expectedCollection = new Collection();
        $this->accommodationRepositoryMock->shouldReceive('all')
            ->once()
            ->andReturn($expectedCollection);

        // Act: Se llama al método del servicio.
        $result = $this->accommodationService->getAll();

        // Assert: Se verifica que el resultado es una instancia de Collection y
        // que es el mismo objeto que el mock retornó.
        $this->assertInstanceOf(Collection::class, $result);
        $this->assertEquals($expectedCollection, $result);
    }

    /**
     * Prueba que el método find llama al método 'find' del repositorio.
     */
    public function test_it_can_find_an_accommodation_by_id()
    {
        // Arrange: Se define el ID a buscar y se crea un mock de la acomodación.
        $accommodationId = 1;
        $expectedAccommodation = Mockery::mock(Accommodation::class);
        $this->accommodationRepositoryMock->shouldReceive('find')
            ->once()
            ->with($accommodationId)
            ->andReturn($expectedAccommodation);

        // Act: Se llama al método del servicio.
        $result = $this->accommodationService->find($accommodationId);

        // Assert: Se verifica que el resultado es la acomodación esperada.
        $this->assertEquals($expectedAccommodation, $result);
    }

    /**
     * Prueba que el método create llama al método 'create' del repositorio.
     */
    public function test_it_can_create_an_accommodation()
    {
        // Arrange: Se definen los datos a crear y se crea un mock de la acomodación.
        $data = ['name' => 'Suite Deluxe', 'price' => 150];
        $expectedAccommodation = Mockery::mock(Accommodation::class);
        $this->accommodationRepositoryMock->shouldReceive('create')
            ->once()
            ->with($data)
            ->andReturn($expectedAccommodation);

        // Act: Se llama al método del servicio.
        $result = $this->accommodationService->create($data);

        // Assert: Se verifica que el resultado es la acomodación esperada.
        $this->assertEquals($expectedAccommodation, $result);
    }

    /**
     * Prueba que el método update llama al método 'update' del repositorio.
     */
    public function test_it_can_update_an_accommodation()
    {
        // Arrange: Se define el ID y los datos a actualizar.
        $accommodationId = 1;
        $data = ['price' => 200];
        $expectedAccommodation = Mockery::mock(Accommodation::class);
        $this->accommodationRepositoryMock->shouldReceive('update')
            ->once()
            ->with($accommodationId, $data)
            ->andReturn($expectedAccommodation);

        // Act: Se llama al método del servicio.
        $result = $this->accommodationService->update($accommodationId, $data);

        // Assert: Se verifica que el resultado es la acomodación esperada.
        $this->assertEquals($expectedAccommodation, $result);
    }

    /**
     * Prueba que el método delete llama al método 'delete' del repositorio.
     */
    public function test_it_can_delete_an_accommodation()
    {
        // Arrange: Se define el ID a eliminar y el comportamiento esperado.
        $accommodationId = 1;
        $this->accommodationRepositoryMock->shouldReceive('delete')
            ->once()
            ->with($accommodationId)
            ->andReturn(true);

        // Act: Se llama al método del servicio.
        $result = $this->accommodationService->delete($accommodationId);

        // Assert: Se verifica que el resultado es verdadero (éxito).
        $this->assertTrue($result);
    }
}
