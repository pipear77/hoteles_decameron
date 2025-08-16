<?php

namespace Tests\Unit;

use App\Models\Accommodation;
use App\Repositories\AccommodationRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Clase de pruebas unitarias para AccommodationRepository.
 *
 * Estas pruebas verifican la correcta interacción del repositorio
 * con la base de datos a través del modelo Eloquent.
 */
class AccommodationRepositoryTest extends TestCase
{
    // Usa RefreshDatabase para migrar y limpiar la base de datos de prueba
    // antes de cada test, asegurando un entorno de prueba aislado.
    use RefreshDatabase;

    protected AccommodationRepository $repository;

    /**
     * Configuración inicial para cada prueba.
     * Se crea una nueva instancia del repositorio.
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new AccommodationRepository();
    }

    /**
     * Prueba que el método 'all' devuelve una colección de todas las acomodaciones.
     */
    public function test_it_can_get_all_accommodations(): void
    {
        // ARRANGE: Se crean 3 acomodaciones en la base de datos.
        Accommodation::factory()->count(3)->create();

        // ACT: Se llama al método 'all'.
        $accommodations = $this->repository->all();

        // ASSERT: Se verifica que se devuelva una colección con la cantidad correcta de elementos.
        $this->assertCount(3, $accommodations);
        $this->assertEquals(3, $accommodations->count());
    }

    /**
     * Prueba que el método 'find' devuelve una acomodación por su ID.
     */
    public function test_it_can_find_an_accommodation_by_id(): void
    {
        // ARRANGE: Se crea una acomodación específica.
        $targetAccommodation = Accommodation::factory()->create();

        // ACT: Se busca la acomodación por su ID.
        $foundAccommodation = $this->repository->find($targetAccommodation->id);

        // ASSERT: Se verifica que se encontró la acomodación correcta.
        $this->assertNotNull($foundAccommodation);
        $this->assertEquals($targetAccommodation->id, $foundAccommodation->id);
    }

    /**
     * Prueba que el método 'find' devuelve null si la acomodación no existe.
     */
    public function test_it_returns_null_if_accommodation_not_found(): void
    {
        // ARRANGE: La base de datos está vacía.
        // ACT: Se busca una acomodación con un ID que no existe.
        $foundAccommodation = $this->repository->find(999);

        // ASSERT: Se verifica que se devuelva null.
        $this->assertNull($foundAccommodation);
    }

    /**
     * Prueba que el método 'create' crea una nueva acomodación.
     */
    public function test_it_can_create_an_accommodation(): void
    {
        // ARRANGE: Se preparan los datos para la nueva acomodación.
        $accommodationData = Accommodation::factory()->raw();

        // ACT: Se crea la nueva acomodación.
        $newAccommodation = $this->repository->create($accommodationData);

        // ASSERT: Se verifica que el registro exista en la base de datos.
        $this->assertNotNull($newAccommodation);
        $this->assertDatabaseHas('accommodations', ['id' => $newAccommodation->id]);
        $this->assertEquals($accommodationData['name'], $newAccommodation->name);
    }

    /**
     * Prueba que el método 'update' actualiza una acomodación existente.
     */
    public function test_it_can_update_an_accommodation(): void
    {
        // ARRANGE: Se crea una acomodación y se definen los nuevos datos.
        $accommodation = Accommodation::factory()->create();
        $updatedData = ['name' => 'Hotel Actualizado'];

        // ACT: Se actualiza la acomodación.
        $updatedAccommodation = $this->repository->update($accommodation->id, $updatedData);

        // ASSERT: Se verifica que el método devuelva la acomodación actualizada
        // y que la base de datos refleje el cambio.
        $this->assertNotNull($updatedAccommodation);
        $this->assertEquals('Hotel Actualizado', $updatedAccommodation->name);
        $this->assertDatabaseHas('accommodations', ['id' => $accommodation->id, 'name' => 'Hotel Actualizado']);
    }

    /**
     * Prueba que el método 'update' devuelve null si la acomodación no existe.
     */
    public function test_update_returns_null_if_accommodation_not_found(): void
    {
        // ARRANGE: Se prepara un ID que no existe.
        $updatedData = ['name' => 'Hotel'];

        // ACT: Se intenta actualizar.
        $updatedAccommodation = $this->repository->update(999, $updatedData);

        // ASSERT: Se verifica que se devuelva null.
        $this->assertNull($updatedAccommodation);
    }

    /**
     * Prueba que el método 'delete' elimina una acomodación.
     */
    public function test_it_can_delete_an_accommodation(): void
    {
        // ARRANGE: Se crea una acomodación para ser eliminada.
        $accommodation = Accommodation::factory()->create();

        // ACT: Se llama al método 'delete'.
        $isDeleted = $this->repository->delete($accommodation->id);

        // ASSERT: Se verifica que el método devuelva true y que el registro ya no exista en la base de datos.
        $this->assertTrue($isDeleted);
        $this->assertDatabaseMissing('accommodations', ['id' => $accommodation->id]);
    }

    /**
     * Prueba que el método 'delete' devuelve false si la acomodación no existe.
     */
    public function test_it_returns_false_if_accommodation_not_deleted(): void
    {
        // ARRANGE: La base de datos está vacía.
        // ACT: Se intenta eliminar una acomodación con un ID que no existe.
        $isDeleted = $this->repository->delete(999);

        // ASSERT: Se verifica que el método devuelva false.
        $this->assertFalse($isDeleted);
    }
}
