<?php

namespace Tests\Unit;

use App\Models\HotelRoomConfiguration;
use App\Repositories\HotelRoomConfigurationRepository;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Mockery;
use Tests\TestCase;

class HotelRoomConfigurationRepositoryTest extends TestCase
{
    /**
     * @var Mockery\MockInterface|HotelRoomConfiguration
     */
    protected $modelMock;

    /**
     * @var HotelRoomConfigurationRepository
     */
    protected $repository;

    /**
     * Set up the test environment.
     * Esta es la clave. Antes de cada test, configuramos el mock del modelo.
     * Esto nos permite "aislar" el repositorio y controlar lo que el modelo "debe hacer".
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        // Creamos un "mock" del modelo. Es un objeto simulado.
        // Ahora podemos controlar su comportamiento y asegurarnos de que el repositorio
        // interactúe con él de la manera esperada.
        $this->modelMock = Mockery::mock(HotelRoomConfiguration::class);

        // Creamos la instancia del repositorio, inyectando el mock del modelo.
        // Esto soluciona el `ArgumentCountError` de raíz.
        $this->repository = new HotelRoomConfigurationRepository($this->modelMock);
    }

    /**
     * Clean up the test environment.
     * Esto asegura que los mocks sean liberados correctamente.
     *
     * @return void
     */
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    /**
     * Test that `all` returns all room configurations.
     *
     * @return void
     */
    public function test_all_returns_all_room_configurations(): void
    {
        // Creamos una colección de Eloquent, no una colección genérica de Laravel.
        // Esto es lo que Eloquent\Model::all() retorna realmente.
        $configurations = new EloquentCollection([
            new HotelRoomConfiguration(),
            new HotelRoomConfiguration(),
        ]);

        // Decimos al mock que cuando se llame a `all()`, debe devolver nuestra colección.
        $this->modelMock->shouldReceive('all')->once()->andReturn($configurations);

        $result = $this->repository->all();

        // Verificamos que el resultado es una instancia de la colección de Eloquent
        // y que el número de elementos es el esperado.
        $this->assertInstanceOf(EloquentCollection::class, $result);
        $this->assertCount(2, $result);
    }

    /**
     * Test that `find` returns a specific room configuration.
     *
     * @return void
     */
    public function test_find_returns_a_specific_room_configuration(): void
    {
        // El mock ahora retorna una instancia del modelo, no un objeto genérico.
        $configuration = new HotelRoomConfiguration();

        // Se espera que el método `find` sea llamado una vez con el ID 1 y devuelva el objeto.
        $this->modelMock->shouldReceive('find')->once()->with(1)->andReturn($configuration);

        $result = $this->repository->find(1);

        $this->assertInstanceOf(HotelRoomConfiguration::class, $result);
        $this->assertEquals($configuration, $result);
    }

    /**
     * Test that `find` returns null if configuration not found.
     *
     * @return void
     */
    public function test_find_returns_null_if_configuration_not_found(): void
    {
        // El mock debe devolver null cuando se llame a `find` con un ID que no existe.
        $this->modelMock->shouldReceive('find')->once()->with(999)->andReturn(null);

        $result = $this->repository->find(999);

        $this->assertNull($result);
    }

    /**
     * Test that `get_by_hotel_id` returns correct configurations.
     *
     * @return void
     */
    public function test_get_by_hotel_id_returns_correct_configurations(): void
    {
        $hotelId = 1;

        // El mock de la cadena de consulta ahora debe retornar una EloquentCollection.
        $configurations = new EloquentCollection([
            new HotelRoomConfiguration(['hotel_id' => $hotelId]),
            new HotelRoomConfiguration(['hotel_id' => $hotelId]),
        ]);

        $queryMock = Mockery::mock();
        $queryMock->shouldReceive('get')->once()->andReturn($configurations);

        $this->modelMock->shouldReceive('where')->once()->with('hotel_id', $hotelId)->andReturn($queryMock);

        $result = $this->repository->getByHotelId($hotelId);

        $this->assertInstanceOf(EloquentCollection::class, $result);
        $this->assertCount(2, $result);
    }

    /**
     * Test that `get_total_room_quantity_by_hotel_id` returns sum of quantities.
     *
     * @return void
     */
    public function test_get_total_room_quantity_by_hotel_id_returns_sum_of_quantities(): void
    {
        $hotelId = 1;
        $totalQuantity = 15;

        // Mockeamos la cadena de métodos `where` y `sum`.
        $queryMock = Mockery::mock();
        $queryMock->shouldReceive('sum')->once()->with('quantity')->andReturn($totalQuantity);

        $this->modelMock->shouldReceive('where')->once()->with('hotel_id', $hotelId)->andReturn($queryMock);

        $result = $this->repository->getTotalRoomQuantityByHotelId($hotelId);

        $this->assertEquals($totalQuantity, $result);
    }

    /**
     * Test that `create` creates a new room configuration.
     *
     * @return void
     */
    public function test_create_creates_a_new_room_configuration(): void
    {
        $data = ['hotel_id' => 1, 'room_type_id' => 1, 'quantity' => 10];
        // El mock ahora retorna una instancia del modelo, no un objeto genérico.
        $newConfiguration = new HotelRoomConfiguration($data);

        // Se espera que el método `create` sea llamado una vez con los datos y devuelva el nuevo objeto.
        $this->modelMock->shouldReceive('create')->once()->with($data)->andReturn($newConfiguration);

        $result = $this->repository->create($data);

        $this->assertInstanceOf(HotelRoomConfiguration::class, $result);
        $this->assertEquals($newConfiguration, $result);
    }

    /**
     * Test that `update` modifies an existing configuration.
     *
     * @return void
     */
    public function test_update_modifies_an_existing_configuration(): void
    {
        $id = 1;
        $data = ['quantity' => 20];

        // Creamos un mock del modelo que va a ser retornado por find().
        $existingConfiguration = Mockery::mock(HotelRoomConfiguration::class);

        // Mockeamos la llamada a update() en ese mock y verificamos que se llama con los datos correctos.
        // No necesitamos que retorne nada, solo verificar que se llama.
        $existingConfiguration->shouldReceive('update')->once()->with($data);

        // El mock de find() debe retornar el mock que creamos arriba.
        $this->modelMock->shouldReceive('find')->once()->with($id)->andReturn($existingConfiguration);

        $result = $this->repository->update($id, $data);

        // Ahora, verificamos que el resultado del repositorio es el mismo mock que esperábamos.
        $this->assertEquals($existingConfiguration, $result);
    }

    /**
     * Test that `update` returns null if configuration not found.
     *
     * @return void
     */
    public function test_update_returns_null_if_configuration_not_found(): void
    {
        $id = 999;
        $data = ['quantity' => 20];

        // Mockeamos que el modelo no encuentra la configuración.
        $this->modelMock->shouldReceive('find')->once()->with($id)->andReturn(null);

        $result = $this->repository->update($id, $data);

        $this->assertNull($result);
    }

    /**
     * Test that `delete` removes a room configuration.
     *
     * @return void
     */
    public function test_delete_removes_a_room_configuration(): void
    {
        $id = 1;
        $existingConfiguration = Mockery::mock(HotelRoomConfiguration::class);
        $existingConfiguration->shouldReceive('delete')->once()->andReturn(true);

        $this->modelMock->shouldReceive('find')->once()->with($id)->andReturn($existingConfiguration);

        $result = $this->repository->delete($id);

        $this->assertTrue($result);
    }

    /**
     * Test that `delete` returns false if configuration not found.
     *
     * @return void
     */
    public function test_delete_returns_false_if_configuration_not_found(): void
    {
        $id = 999;

        $this->modelMock->shouldReceive('find')->once()->with($id)->andReturn(null);

        $result = $this->repository->delete($id);

        $this->assertFalse($result);
    }
}
