<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\RoomType;
use App\Repositories\RoomTypeRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * Pruebas Unitarias para la clase RoomTypeRepository.
 * Estas pruebas se centran en verificar que los métodos del repositorio
 * interactúan correctamente con el modelo RoomType y la base de datos,
 * asegurando la persistencia y recuperación de datos.
 *
 * Utiliza el trait `RefreshDatabase` para garantizar un estado de base de datos
 * limpio antes de cada prueba, eliminando la necesidad de mocks para el modelo.
 */
class RoomTypeRepositoryTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @var RoomTypeRepository
     */
    protected RoomTypeRepository $repository;

    /**
     * Configuración inicial para cada prueba.
     * Se crea una instancia del repositorio.
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new RoomTypeRepository();
    }

    /**
     * Prueba que el método 'all' recupera todos los tipos de habitación.
     */
    public function test_it_can_get_all_room_types(): void
    {
        // Se crean 3 tipos de habitación en la base de datos para la prueba.
        RoomType::factory()->count(3)->create();

        $roomTypes = $this->repository->all();

        // Se verifica que la colección no está vacía y tiene el tamaño correcto.
        $this->assertCount(3, $roomTypes);
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Collection::class, $roomTypes);
    }

    /**
     * Prueba que el método 'find' encuentra un tipo de habitación por su ID.
     */
    public function test_it_can_find_a_room_type_by_id(): void
    {
        // Se crea un tipo de habitación específico para la prueba.
        $roomType = RoomType::factory()->create();

        $foundRoomType = $this->repository->find($roomType->id);

        // Se verifica que el tipo de habitación encontrado es el mismo que el creado.
        $this->assertEquals($roomType->id, $foundRoomType->id);
        $this->assertEquals($roomType->name, $foundRoomType->name);
    }

    /**
     * Prueba que el método 'find' devuelve null si el ID no existe.
     */
    public function test_find_returns_null_if_not_found(): void
    {
        $nonExistentId = 999;

        $foundRoomType = $this->repository->find($nonExistentId);

        // Se verifica que el resultado es null.
        $this->assertNull($foundRoomType);
    }

    /**
     * Prueba que el método 'create' crea un nuevo tipo de habitación.
     */
    public function test_it_can_create_a_new_room_type(): void
    {
        $data = [
            'name' => 'Suite de Lujo',
            'description' => 'Habitación espaciosa con vistas panorámicas.',
            'capacity' => 2,
        ];

        $newRoomType = $this->repository->create($data);

        // Se verifica que la instancia devuelta es un RoomType.
        $this->assertInstanceOf(RoomType::class, $newRoomType);
        // Se verifica que el tipo de habitación existe en la base de datos con los datos correctos.
        $this->assertDatabaseHas('room_types', ['name' => 'Suite de Lujo']);
    }

    /**
     * Prueba que el método 'update' actualiza un tipo de habitación existente.
     */
    public function test_it_can_update_an_existing_room_type(): void
    {
        // Se crea un tipo de habitación inicial.
        $roomType = RoomType::factory()->create(['name' => 'Habitación Doble']);
        $updateData = ['name' => 'Doble Twin'];

        $updatedRoomType = $this->repository->update($roomType->id, $updateData);

        // Se verifica que la instancia devuelta tiene los datos actualizados.
        $this->assertEquals('Doble Twin', $updatedRoomType->name);
        // Se verifica que los datos se han actualizado en la base de datos.
        $this->assertDatabaseHas('room_types', ['id' => $roomType->id, 'name' => 'Doble Twin']);
    }

    /**
     * Prueba que el método 'update' devuelve null si el tipo de habitación no existe.
     */
    public function test_update_returns_null_if_not_found(): void
    {
        $nonExistentId = 999;
        $updateData = ['name' => 'Habitación Individual'];

        $updatedRoomType = $this->repository->update($nonExistentId, $updateData);

        // Se verifica que el resultado es null.
        $this->assertNull($updatedRoomType);
    }

    /**
     * Prueba que el método 'delete' elimina un tipo de habitación.
     */
    public function test_it_can_delete_a_room_type(): void
    {
        // Se crea un tipo de habitación para eliminar.
        $roomType = RoomType::factory()->create();

        $isDeleted = $this->repository->delete($roomType->id);

        // Se verifica que el método devuelve true.
        $this->assertTrue($isDeleted);
        // Se verifica que el registro no existe en la base de datos.
        $this->assertDatabaseMissing('room_types', ['id' => $roomType->id]);
    }

    /**
     * Prueba que el método 'delete' devuelve false si el tipo de habitación no existe.
     */
    public function test_delete_returns_false_if_not_found(): void
    {
        $nonExistentId = 999;

        $isDeleted = $this->repository->delete($nonExistentId);

        // Se verifica que el resultado es false.
        $this->assertFalse($isDeleted);
    }
}
