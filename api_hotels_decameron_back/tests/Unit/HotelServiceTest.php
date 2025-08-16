<?php

namespace Tests\Unit;

use Mockery;
use Tests\TestCase;
use App\Models\Hotel;
use App\Services\HotelService;
use Illuminate\Database\Eloquent\Collection;
use App\Repositories\HotelRepositoryInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * Pruebas unitarias para la clase HotelService.
 * Estas pruebas se enfocan en verificar la lógica de negocio y la delegación
 * de responsabilidades al repositorio. Se utiliza Mockery para simular
 * las interacciones con el repositorio y evitar la dependencia de la base de datos.
 */
class HotelServiceTest extends TestCase
{
    use RefreshDatabase;

    private HotelRepositoryInterface $repository;
    private HotelService $service;

    /**
     * Configuración inicial para cada prueba.
     */
    protected function setUp(): void
    {
        parent::setUp();
        // Se crea un mock del repositorio.
        $this->repository = Mockery::mock(HotelRepositoryInterface::class);
        // Se inyecta el mock en el servicio.
        $this->service = new HotelService($this->repository);
    }

    /**
     * Limpia los mocks después de cada prueba.
     */
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    /**
     * Prueba que el método 'getAll' delega al repositorio cuando no se pasa un nombre.
     */
    public function test_get_all_delegates_to_repository_when_no_name_is_provided(): void
    {
        // ARRANGE: Se crea una colección de hoteles falsa.
        $hotels = Hotel::factory()->count(2)->make();
        // Se simula la llamada al método 'all' del repositorio.
        $this->repository->shouldReceive('all')
            ->once()
            ->andReturn($hotels);

        // ACT: Se llama al método del servicio sin un nombre.
        $result = $this->service->getAll();

        // ASSERT: Se verifica que el resultado es la colección esperada.
        $this->assertEquals($hotels, $result);
    }

    /**
     * Prueba que el método 'getById' delega al repositorio.
     */
    public function test_get_by_id_delegates_to_repository(): void
    {
        // ARRANGE: Se crea un hotel falso.
        $hotel = Hotel::factory()->make(['id' => 1]);
        // Se simula la llamada al método 'find' del repositorio con el ID correcto.
        $this->repository->shouldReceive('find')
            ->once()
            ->with(1)
            ->andReturn($hotel);

        // ACT: Se llama al método del servicio con un ID.
        $result = $this->service->getById(1);

        // ASSERT: Se verifica que el resultado es el hotel esperado.
        $this->assertEquals($hotel, $result);
    }

    /**
     * Prueba que el método 'create' delega al repositorio.
     */
    public function test_create_delegates_to_repository(): void
    {
        // ARRANGE: Se preparan datos de hotel y se crea un hotel falso.
        $data = ['name' => 'Hotel Creado', 'city' => 'Bogota'];
        $hotel = new Hotel($data);
        // Se simula la llamada al método 'create' del repositorio con los datos correctos.
        $this->repository->shouldReceive('create')
            ->once()
            ->with($data)
            ->andReturn($hotel);

        // ACT: Se llama al método del servicio con los datos.
        $result = $this->service->create($data);

        // ASSERT: Se verifica que el resultado es el hotel creado esperado.
        $this->assertEquals($hotel, $result);
    }

    /**
     * Prueba que el método 'update' delega al repositorio.
     */
    public function test_update_delegates_to_repository(): void
    {
        // ARRANGE: Se prepara un hotel existente y datos de actualización.
        $hotel = Hotel::factory()->make(['id' => 1]);
        $data = ['name' => 'Hotel Actualizado'];
        // Se simula la llamada al método 'update' del repositorio con el ID y datos correctos.
        $this->repository->shouldReceive('update')
            ->once()
            ->with(1, $data)
            ->andReturn($hotel->forceFill($data));

        // ACT: Se llama al método del servicio para actualizar.
        $result = $this->service->update(1, $data);

        // ASSERT: Se verifica que el resultado contiene los datos actualizados.
        $this->assertEquals('Hotel Actualizado', $result->name);
    }

    /**
     * Prueba que el método 'delete' delega al repositorio.
     */
    public function test_delete_delegates_to_repository(): void
    {
        // ARRANGE: Se simula la llamada al método 'delete' del repositorio.
        $this->repository->shouldReceive('delete')
            ->once()
            ->with(1)
            ->andReturn(true);

        // ACT: Se llama al método del servicio para eliminar.
        $result = $this->service->delete(1);

        // ASSERT: Se verifica que el resultado sea true.
        $this->assertTrue($result);
    }
}
