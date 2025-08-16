<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Hotel;
use App\Repositories\HotelRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * Pruebas unitarias para la clase HotelRepository.
 * Estas pruebas se enfocan en la lógica de persistencia y la interacción
 * con el modelo Eloquent, asegurando que las operaciones CRUD se realicen
 * correctamente en la base de datos.
 */
class HotelRepositoryTest extends TestCase
{
    use RefreshDatabase;

    private HotelRepository $repository;

    /**
     * Se inicializa el repositorio antes de cada prueba.
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new HotelRepository();
    }

    /**
     * Prueba que el método 'all' devuelve todos los hoteles.
     */
    public function test_all_returns_all_hotels(): void
    {
        // ARRANGE: Crea 3 hoteles en la base de datos.
        Hotel::factory()->count(3)->create();

        // ACT: Llama al método all() del repositorio.
        $hotels = $this->repository->all();

        // ASSERT: Verifica que la colección devuelta tenga 3 elementos.
        $this->assertCount(3, $hotels);
    }

    /**
     * Prueba que el método 'find' devuelve un hotel existente.
     */
    public function test_find_returns_a_hotel_by_id(): void
    {
        // ARRANGE: Crea un hotel específico en la base de datos.
        $hotel = Hotel::factory()->create();

        // ACT: Llama al método find() con el ID del hotel.
        $foundHotel = $this->repository->find($hotel->id);

        // ASSERT: Verifica que el hotel encontrado no es nulo y que sus atributos coinciden.
        $this->assertNotNull($foundHotel);
        $this->assertEquals($hotel->id, $foundHotel->id);
    }

    /**
     * Prueba que el método 'find' devuelve null si el hotel no existe.
     */
    public function test_find_returns_null_for_non_existent_hotel(): void
    {
        // ARRANGE: No hay hoteles en la base de datos.
        // ACT: Llama al método find() con un ID que no existe.
        $foundHotel = $this->repository->find(999);

        // ASSERT: Verifica que el resultado es nulo.
        $this->assertNull($foundHotel);
    }

    /**
     * Prueba que el método 'create' crea y almacena un nuevo hotel.
     */
    public function test_create_stores_a_new_hotel(): void
    {
        // Se utiliza el método `raw` del factory para obtener un array de datos
        // válidos, incluyendo el campo `rooms_total`. Esto asegura que los datos
        // de prueba sean consistentes y no violen las restricciones de la base de datos.
        $data = Hotel::factory()->raw();

        $newHotel = $this->repository->create($data);

        $this->assertInstanceOf(Hotel::class, $newHotel);
        $this->assertDatabaseHas('hotels', ['id' => $newHotel->id]);
    }

    /**
     * Prueba que el método 'update' modifica un hotel existente.
     */
    public function test_update_modifies_an_existing_hotel(): void
    {
        // ARRANGE: Crea un hotel y prepara los datos para la actualización.
        $hotel = Hotel::factory()->create();
        $updatedData = [
            'name' => 'Hotel Renombrado',
            'city' => 'Bogota',
        ];

        // ACT: Llama al método update() del repositorio.
        $updatedHotel = $this->repository->update($hotel->id, $updatedData);

        // ASSERT:
        // 1. Verifica que el objeto devuelto no es nulo.
        $this->assertNotNull($updatedHotel);
        // 2. Verifica que los datos se han actualizado en la base de datos.
        $this->assertDatabaseHas('hotels', $updatedData);
    }

    /**
     * Prueba que el método 'update' devuelve null si el hotel no existe.
     */
    public function test_update_returns_null_for_non_existent_hotel(): void
    {
        // ARRANGE: Prepara datos de actualización, pero no hay hotel en la DB.
        $data = ['name' => 'Hotel Fantasma'];

        // ACT: Llama a update() con un ID que no existe.
        $updatedHotel = $this->repository->update(999, $data);

        // ASSERT: Verifica que el resultado es nulo.
        $this->assertNull($updatedHotel);
    }

    /**
     * Prueba que el método 'delete' elimina un hotel de la base de datos.
     */
    public function test_delete_removes_a_hotel_from_the_database(): void
    {
        // ARRANGE: Crea un hotel para eliminar.
        $hotel = Hotel::factory()->create();

        // ACT: Llama al método delete() del repositorio.
        $deleted = $this->repository->delete($hotel->id);

        // ASSERT:
        // 1. Verifica que el método devuelve true.
        $this->assertTrue($deleted);
        // 2. Verifica que el hotel ya no existe en la base de datos.
        $this->assertDatabaseMissing('hotels', ['id' => $hotel->id]);
    }

    /**
     * Prueba que el método 'delete' devuelve false si el hotel no existe.
     */
    public function test_delete_returns_false_for_non_existent_hotel(): void
    {
        // ACT: Intenta eliminar un hotel con un ID que no existe.
        $deleted = $this->repository->delete(999);

        // ASSERT: Verifica que el método devuelve false.
        $this->assertFalse($deleted);
    }
}
