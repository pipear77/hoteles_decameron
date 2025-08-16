<?php
// src/tests/Unit/AccommodationRepositoryTest.php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Accommodation;
use App\Repositories\AccommodationRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * Clase de pruebas unitarias para AccommodationRepository.
 *
 * Estas pruebas se enfocan en los métodos necesarios para que el repositorio
 * sirva como una capa de abstracción para los servicios que lo consumen.
 */
class AccommodationRepositoryTest extends TestCase
{
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
}
