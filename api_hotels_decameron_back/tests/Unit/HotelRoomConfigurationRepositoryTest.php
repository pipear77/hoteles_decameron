<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Hotel;
use App\Models\HotelRoomConfiguration;
use App\Repositories\HotelRoomConfigurationRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * Clase de pruebas unitarias para HotelRoomConfigurationRepository.
 * Estas pruebas se centran en verificar la correcta interacción del repositorio
 * con la base de datos a través del modelo Eloquent.
 */
class HotelRoomConfigurationRepositoryTest extends TestCase
{
    // Utiliza el trait RefreshDatabase para migrar y resetear la base de datos en cada test.
    use RefreshDatabase;

    private HotelRoomConfigurationRepository $repository;

    /**
     * Configuración inicial para cada prueba.
     * Se crea una instancia del repositorio.
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new HotelRoomConfigurationRepository();
    }

    /**
     * @test
     * Prueba que el método 'all' devuelve una colección de todas las configuraciones.
     */
    public function all_returns_all_room_configurations(): void
    {
        // ARRANGE: Se crean 5 configuraciones de habitación en la base de datos.
        HotelRoomConfiguration::factory()->count(5)->create();

        // ACT: Se llama al método 'all'.
        $configurations = $this->repository->all();

        // ASSERT: Se verifica que se devuelva una colección con 5 elementos.
        $this->assertCount(5, $configurations);
        $this->assertEquals(5, $configurations->count());
    }

    /**
     * @test
     * Prueba que el método 'find' devuelve una configuración por su ID.
     */
    public function find_returns_a_specific_room_configuration(): void
    {
        // ARRANGE: Se crea una configuración específica y otras para asegurar el aislamiento.
        $targetConfiguration = HotelRoomConfiguration::factory()->create();
        HotelRoomConfiguration::factory()->count(2)->create();

        // ACT: Se busca la configuración por su ID.
        $foundConfiguration = $this->repository->find($targetConfiguration->id);

        // ASSERT: Se verifica que se encontró la configuración correcta.
        $this->assertNotNull($foundConfiguration);
        $this->assertEquals($targetConfiguration->id, $foundConfiguration->id);
    }

    /**
     * @test
     * Prueba que el método 'find' devuelve null si la configuración no existe.
     */
    public function find_returns_null_if_configuration_not_found(): void
    {
        // ARRANGE: La base de datos está vacía.
        // ACT: Se busca una configuración con un ID que no existe.
        $foundConfiguration = $this->repository->find(999);

        // ASSERT: Se verifica que se devuelva null.
        $this->assertNull($foundConfiguration);
    }

    /**
     * @test
     * Prueba que el método 'getByHotelId' devuelve las configuraciones de un hotel específico.
     */
    public function get_by_hotel_id_returns_correct_configurations(): void
    {
        // ARRANGE: Se crean hoteles y configuraciones para simular el escenario.
        $hotel1 = Hotel::factory()->create();
        $hotel2 = Hotel::factory()->create();
        HotelRoomConfiguration::factory()->count(3)->create(['hotel_id' => $hotel1->id]);
        HotelRoomConfiguration::factory()->count(2)->create(['hotel_id' => $hotel2->id]);

        // ACT: Se obtienen las configuraciones del hotel 1.
        $configurations = $this->repository->getByHotelId($hotel1->id);

        // ASSERT: Se verifica que la cantidad sea la esperada y que todos pertenezcan al mismo hotel.
        $this->assertCount(3, $configurations);
        $configurations->each(function ($config) use ($hotel1) {
            $this->assertEquals($hotel1->id, $config->hotel_id);
        });
    }

    /**
     * @test
     * Prueba que el método 'getTotalRoomQuantityByHotelId' suma las cantidades de habitaciones de un hotel específico.
     */
    public function get_total_room_quantity_by_hotel_id_returns_sum_of_quantities(): void
    {
        // ARRANGE: Se crea un hotel y varias configuraciones con diferentes cantidades.
        $hotel = Hotel::factory()->create();
        HotelRoomConfiguration::factory()->create(['hotel_id' => $hotel->id, 'quantity' => 10]);
        HotelRoomConfiguration::factory()->create(['hotel_id' => $hotel->id, 'quantity' => 5]);
        HotelRoomConfiguration::factory()->create(['hotel_id' => $hotel->id, 'quantity' => 12]);
        // Se crea otra configuración para otro hotel para asegurar que no se incluya.
        HotelRoomConfiguration::factory()->create(['quantity' => 20]);

        // ACT: Se cuenta la cantidad total de habitaciones para el hotel.
        $totalQuantity = $this->repository->getTotalRoomQuantityByHotelId($hotel->id);

        // ASSERT: Se verifica que la suma sea correcta (10 + 5 + 12 = 27).
        $this->assertEquals(27, $totalQuantity);
    }

    /**
     * @test
     * Prueba que el método 'create' crea una nueva configuración de habitación.
     */
    public function create_creates_a_new_room_configuration(): void
    {
        // ARRANGE: Se preparan los datos para la nueva configuración.
        $data = HotelRoomConfiguration::factory()->raw();
        $initialCount = HotelRoomConfiguration::count();

        // ACT: Se crea la nueva configuración.
        $newConfiguration = $this->repository->create($data);

        // ASSERT: Se verifica que se ha creado un nuevo registro en la base de datos y que los datos son correctos.
        $this->assertCount($initialCount + 1, HotelRoomConfiguration::all());
        $this->assertDatabaseHas('hotel_room_configurations', ['id' => $newConfiguration->id]);
        $this->assertEquals($data['quantity'], $newConfiguration->quantity);
    }

    /**
     * @test
     * Prueba que el método 'update' actualiza una configuración existente.
     */
    public function update_modifies_an_existing_configuration(): void
    {
        // ARRANGE: Se crea una configuración y se definen los nuevos datos.
        $configuration = HotelRoomConfiguration::factory()->create();
        $updatedData = ['quantity' => 20];

        // ACT: Se actualiza la configuración.
        $updatedConfiguration = $this->repository->update($configuration->id, $updatedData);

        // ASSERT: Se verifica que la configuración fue actualizada correctamente en la base de datos.
        $this->assertNotNull($updatedConfiguration);
        $this->assertEquals(20, $updatedConfiguration->quantity);
        $this->assertDatabaseHas('hotel_room_configurations', ['id' => $configuration->id, 'quantity' => 20]);
    }

    /**
     * @test
     * Prueba que el método 'update' devuelve null si la configuración no existe.
     */
    public function update_returns_null_if_configuration_not_found(): void
    {
        // ARRANGE: Se prepara un ID que no existe.
        $updatedData = ['quantity' => 15];

        // ACT: Se intenta actualizar.
        $updatedConfiguration = $this->repository->update(999, $updatedData);

        // ASSERT: Se verifica que se devuelva null.
        $this->assertNull($updatedConfiguration);
    }

    /**
     * @test
     * Prueba que el método 'delete' elimina una configuración de habitación.
     */
    public function delete_removes_a_room_configuration(): void
    {
        // ARRANGE: Se crea una configuración para ser eliminada.
        $configuration = HotelRoomConfiguration::factory()->create();

        // ACT: Se llama al método 'delete'.
        $isDeleted = $this->repository->delete($configuration->id);

        // ASSERT: Se verifica que el método devuelva true y que el registro ya no exista en la base de datos.
        $this->assertTrue($isDeleted);
        $this->assertDatabaseMissing('hotel_room_configurations', ['id' => $configuration->id]);
    }

    /**
     * @test
     * Prueba que el método 'delete' devuelve false si la configuración no existe.
     */
    public function delete_returns_false_if_configuration_not_found(): void
    {
        // ARRANGE: La base de datos está vacía.
        // ACT: Se intenta eliminar una configuración con un ID que no existe.
        $isDeleted = $this->repository->delete(999);

        // ASSERT: Se verifica que el método devuelva false.
        $this->assertFalse($isDeleted);
    }
}
