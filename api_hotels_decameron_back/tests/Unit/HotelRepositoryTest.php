<?php
// src/tests/Unit/HotelRepositoryTest.php

namespace Tests\Unit;

use Mockery;
use Tests\TestCase;
use App\Models\Hotel;
use App\Repositories\HotelRepository;
use Illuminate\Database\Eloquent\Collection;

/**
 * Pruebas unitarias para la clase HotelRepository.
 * Estas pruebas verifican la interacción del repositorio con el modelo Eloquent,
 * asegurando que delega correctamente las responsabilidades de persistencia.
 * Se utiliza Mockery para aislar el repositorio de la base de datos.
 */
class HotelRepositoryTest extends TestCase
{
    private $hotelMock;
    private HotelRepository $repository;

    /**
     * Configuración inicial para cada prueba.
     */
    protected function setUp(): void
    {
        parent::setUp();
        // Se crea un mock del modelo Hotel.
        $this->hotelMock = Mockery::mock(Hotel::class);
        // Se inyecta el mock en el constructor del repositorio.
        $this->repository = new HotelRepository($this->hotelMock);
    }

    /**
     * Limpia los mocks después de cada prueba.
     */
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    /** @test */
    public function it_can_get_a_list_of_all_hotels(): void
    {
        // ARRANGE: Se simula una colección de hoteles.
        $hotels = new Collection([new Hotel(), new Hotel(), new Hotel()]);
        // Se espera que el método 'all' se llame una vez y devuelva la colección.
        $this->hotelMock->shouldReceive('all')->once()->andReturn($hotels);

        // ACT: Llama al método del repositorio.
        $result = $this->repository->all();

        // ASSERT: Verifica que el resultado sea la colección esperada.
        $this->assertCount(3, $result);
        $this->assertInstanceOf(Collection::class, $result);
    }

    /** @test */
    public function it_can_find_a_hotel_by_id(): void
    {
        // ARRANGE: Se simula la búsqueda de un hotel por ID.
        $hotel = new Hotel(['id' => 1]);
        $this->hotelMock->shouldReceive('find')->once()->with(1)->andReturn($hotel);

        // ACT: Llama al método del repositorio.
        $result = $this->repository->find(1);

        // ASSERT: Verifica que el resultado sea el objeto esperado.
        $this->assertEquals($hotel, $result);
    }

    /** @test */
    public function it_returns_null_if_hotel_is_not_found(): void
    {
        // ARRANGE: Se simula la búsqueda fallida.
        $this->hotelMock->shouldReceive('find')->once()->with(999)->andReturn(null);

        // ACT: Llama al método del repositorio.
        $result = $this->repository->find(999);

        // ASSERT: Verifica que el resultado es nulo.
        $this->assertNull($result);
    }

    /** @test */
    public function it_can_create_a_new_hotel(): void
    {
        // ARRANGE: Se preparan los datos para la creación.
        $data = ['name' => 'Hotel Creado'];
        $createdHotel = new Hotel($data);
        // Se espera que el método 'create' del mock del modelo se llame con los datos correctos.
        $this->hotelMock->shouldReceive('create')->once()->with($data)->andReturn($createdHotel);

        // ACT: Llama al método del repositorio.
        $result = $this->repository->create($data);

        // ASSERT: Verifica que el objeto devuelto es el esperado.
        $this->assertEquals($createdHotel, $result);
    }

    /** @test */
    public function it_can_update_an_existing_hotel(): void
    {
        // ARRANGE: Se simula la actualización de un hotel.
        $hotel = Mockery::mock(Hotel::class);
        $updatedData = ['name' => 'Hotel Actualizado'];
        $hotel->shouldReceive('update')->once()->with($updatedData)->andReturn(true);

        // Se simula la búsqueda del hotel por ID, y se devuelve el mock del hotel.
        $this->hotelMock->shouldReceive('find')->once()->with(1)->andReturn($hotel);

        // ACT: Llama al método del repositorio.
        $result = $this->repository->update(1, $updatedData);

        // ASSERT: Verifica que el resultado sea el objeto hotel actualizado.
        $this->assertEquals($hotel, $result);
    }

    /** @test */
    public function it_returns_null_on_update_of_non_existent_hotel(): void
    {
        // ARRANGE: Se simula la búsqueda de un hotel que no existe.
        $this->hotelMock->shouldReceive('find')->once()->with(999)->andReturn(null);
        $updatedData = ['name' => 'Hotel Fantasma'];

        // ACT: Llama al método del repositorio.
        $result = $this->repository->update(999, $updatedData);

        // ASSERT: Verifica que el resultado sea nulo.
        $this->assertNull($result);
    }

    /** @test */
    public function it_can_delete_an_existing_hotel(): void
    {
        // ARRANGE: Se simula la búsqueda y eliminación de un hotel.
        $hotel = Mockery::mock(Hotel::class);
        $hotel->shouldReceive('delete')->once()->andReturn(true);
        $this->hotelMock->shouldReceive('find')->once()->with(1)->andReturn($hotel);

        // ACT: Llama al método del repositorio.
        $result = $this->repository->delete(1);

        // ASSERT: Verifica que el resultado sea true.
        $this->assertTrue($result);
    }

    /** @test */
    public function it_returns_false_on_delete_of_non_existent_hotel(): void
    {
        // ARRANGE: Se simula la búsqueda de un hotel que no existe.
        $this->hotelMock->shouldReceive('find')->once()->with(999)->andReturn(null);

        // ACT: Llama al método del repositorio.
        $result = $this->repository->delete(999);

        // ASSERT: Verifica que el resultado sea false.
        $this->assertFalse($result);
    }
}
