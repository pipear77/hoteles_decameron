<?php
// src/tests/Unit/RoomTypeRepositoryTest.php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\RoomType;
use App\Repositories\RoomTypeRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * Clase de pruebas unitarias para RoomTypeRepository.
 *
 * Estas pruebas se enfocan en los métodos necesarios para que el repositorio
 * sirva como una capa de abstracción para los servicios que lo consumen.
 * Se adhieren a los principios YAGNI y KISS al no probar funcionalidades
 * que no son requeridas por el proyecto.
 */
class RoomTypeRepositoryTest extends TestCase
{
    use RefreshDatabase;

    protected RoomTypeRepository $repository;

    /**
     * Configuración inicial para cada prueba.
     * Se crea una nueva instancia del repositorio.
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new RoomTypeRepository();
    }

    /**
     * Prueba que el método 'all' devuelve una colección de todos los tipos de habitación.
     */
    public function test_it_can_get_all_room_types(): void
    {
        // ARRANGE: Se crean 3 tipos de habitación en la base de datos.
        RoomType::factory()->count(3)->create();

        // ACT: Se llama al método 'all'.
        $roomTypes = $this->repository->all();

        // ASSERT: Se verifica que se devuelva una colección con la cantidad correcta de elementos.
        $this->assertCount(3, $roomTypes);
        $this->assertEquals(3, $roomTypes->count());
    }

    /**
     * Prueba que el método 'find' devuelve un tipo de habitación por su ID.
     */
    public function test_it_can_find_a_room_type_by_id(): void
    {
        // ARRANGE: Se crea un tipo de habitación específico.
        $targetRoomType = RoomType::factory()->create();

        // ACT: Se busca el tipo de habitación por su ID.
        $foundRoomType = $this->repository->find($targetRoomType->id);

        // ASSERT: Se verifica que se encontró el tipo de habitación correcto.
        $this->assertNotNull($foundRoomType);
        $this->assertEquals($targetRoomType->id, $foundRoomType->id);
    }

    /**
     * Prueba que el método 'find' devuelve null si el tipo de habitación no existe.
     */
    public function test_it_returns_null_if_room_type_not_found(): void
    {
        // ARRANGE: La base de datos está vacía.
        // ACT: Se busca un tipo de habitación con un ID que no existe.
        $foundRoomType = $this->repository->find(999);

        // ASSERT: Se verifica que se devuelva null.
        $this->assertNull($foundRoomType);
    }
}
