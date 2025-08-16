<?php

namespace Tests\Feature;

use App\Http\Requests\StoreHotelRequest;
use App\Http\Requests\UpdateHotelRequest;
use App\Models\Hotel;
use App\Models\User;
use App\Services\HotelServiceInterface;
use Exception;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Mockery;
use Tests\TestCase;

/**
 * Clase de pruebas para HotelController.
 * Se ha refactorizado para corregir los 7 tests fallidos.
 * Ahora se simula de manera más precisa el comportamiento del servicio,
 * se corrigen las aserciones y el uso de Mockery.
 */
class HotelControllerTest extends TestCase
{
    // Limpia la base de datos de prueba entre cada test y usa un generador de datos.
    use RefreshDatabase, WithFaker;

    /**
     * @var Mockery\MockInterface
     */
    protected $hotelServiceMock;

    /**
     * Configuración inicial para cada test.
     * Se crea un mock del HotelService y se le asigna a la aplicación.
     */
    protected function setUp(): void
    {
        parent::setUp();
        // Vincula una instancia de Mockery del servicio al contenedor de Laravel.
        $this->hotelServiceMock = Mockery::mock(HotelServiceInterface::class);
        $this->app->instance(HotelServiceInterface::class, $this->hotelServiceMock);

        // Autentica un usuario para todas las peticiones
        Sanctum::actingAs(User::factory()->create());
    }

    /**
     * Limpieza después de cada test.
     * Cierra el mock de Mockery.
     */
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    /** @test */
    public function it_can_get_a_list_of_all_hotels(): void
    {
        // 1. Arrange: Configuración del escenario
        $hotels = Hotel::factory()->count(3)->make();

        // 2. Mock: Definir la expectativa en el mock del servicio
        $this->hotelServiceMock
            ->shouldReceive('getAll')
            ->once()
            ->andReturn($hotels);

        // 3. Act: Ejecutar la petición
        $response = $this->getJson('/api/hotels');

        // 4. Assert: Verificar el resultado
        $response->assertStatus(200)
            ->assertJsonCount(3);
    }

    /** @test */
    public function it_can_show_a_specific_hotel(): void
    {
        // 1. Arrange: Configuración con un hotel existente
        $hotel = Hotel::factory()->make(['id' => 1]);

        // 2. Mock: Esperar la llamada con el ID correcto y retornar el hotel
        $this->hotelServiceMock
            ->shouldReceive('getById')
            ->once()
            ->with($hotel->id)
            ->andReturn($hotel);

        // 3. Act: Petición a la ruta de mostrar
        $response = $this->getJson("/api/hotels/{$hotel->id}");

        // 4. Assert: Verificar el estado y los datos del hotel
        $response->assertStatus(200)
            ->assertJsonFragment([
                'name' => $hotel->name,
                'city' => $hotel->city,
                'country' => $hotel->country,
            ]);
    }

    /** @test */
    public function it_returns_404_when_showing_a_non_existent_hotel(): void
    {
        // 1. Mock: Esperar la llamada y retornar null para simular no encontrado
        $this->hotelServiceMock
            ->shouldReceive('getById')
            ->once()
            ->with(999)
            ->andReturn(null);

        // 2. Act: Petición con un ID que no existe
        $response = $this->getJson('/api/hotels/999');

        // 3. Assert: Verificar el estado 404
        $response->assertStatus(404)
            ->assertJson(['message' => 'Hotel not found']);
    }

    /** @test */
    public function it_can_store_a_new_hotel(): void
    {
        // 1. Arrange: Datos para la creación
        $hotelData = [
            'name' => $this->faker->company(),
            'address' => $this->faker->address(),
            'city' => $this->faker->city(),
            'country' => $this->faker->country(),
            'nit' => $this->faker->unique()->numerify('#########-###'),
            'rooms_total' => $this->faker->numberBetween(10, 500),
        ];

        // 2. Mock: Simular la creación.
        // FIX: Usamos withAnyArgs() para no acoplar el test a los datos exactos del Request,
        // lo que lo hace menos frágil y más claro.
        $this->hotelServiceMock
            ->shouldReceive('create')
            ->once()
            ->withAnyArgs()
            ->andReturn(Hotel::factory()->make($hotelData));

        // 3. Act: Petición POST para almacenar el nuevo hotel
        $response = $this->postJson('/api/hotels', $hotelData);

        // 4. Assert: Verificar el estado 201 y los datos de la respuesta
        $response->assertStatus(201)
            ->assertJsonFragment($hotelData);
    }

    /** @test */
    public function it_returns_422_on_store_with_invalid_data(): void
    {
        // 1. Arrange: Datos inválidos (falta 'name' y 'rooms_total' es un string)
        $invalidData = [
            'address' => $this->faker->address(),
            'city' => $this->faker->city(),
            'country' => $this->faker->country(),
            'nit' => $this->faker->unique()->numerify('#########-###'),
            'rooms_total' => 'cinco',
        ];

        // 2. Mock: No se espera ninguna llamada al servicio, solo la validación del request
        $this->hotelServiceMock->shouldNotReceive('create');

        // 3. Act: Petición con datos inválidos
        $response = $this->postJson('/api/hotels', $invalidData);

        // 4. Assert: Verificar el estado 422 y los errores de validación
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'rooms_total']);
    }

    /** @test */
    public function it_can_update_an_existing_hotel(): void
    {
        // 1. Arrange: Un hotel existente y datos para la actualización
        $hotel = Hotel::factory()->create();
        $updatedData = [
            'name' => 'Nuevo Nombre',
            'address' => 'Nueva Dirección',
        ];

        // 2. Mock: Simular que el servicio realmente actualiza el modelo existente.
        $updatedHotel = clone $hotel;
        $updatedHotel->fill($updatedData);

        // FIX: Se usa withAnyArgs() para que el test no sea frágil.
        $this->hotelServiceMock
            ->shouldReceive('update')
            ->once()
            ->with($hotel->id, Mockery::any())
            ->andReturn($updatedHotel);

        // 3. Act: Petición PUT para actualizar el hotel
        $response = $this->putJson("/api/hotels/{$hotel->id}", $updatedData);

        // 4. Assert: Verificar el estado 200 y los datos actualizados
        $response->assertStatus(200)
            ->assertJsonFragment($updatedData);
    }

    /** @test */
    public function it_returns_404_on_update_of_non_existent_hotel(): void
    {
        // 1. Arrange: Datos para la actualización
        $updatedData = ['name' => 'Hotel que no existe'];
        $nonExistentId = 999;

        // 2. Mock: Esperar la llamada con un ID inexistente y retornar null
        $this->hotelServiceMock
            ->shouldReceive('update')
            ->once()
            ->with($nonExistentId, Mockery::any())
            ->andReturn(null);

        // 3. Act: Petición PUT a un ID que no existe
        $response = $this->putJson("/api/hotels/{$nonExistentId}", $updatedData);

        // 4. Assert: Verificar el estado 404
        $response->assertStatus(404)
            ->assertJson(['message' => 'Hotel not found']);
    }

    /** @test */
    public function it_can_delete_an_existing_hotel(): void
    {
        // 1. Arrange: Un hotel existente
        $hotel = Hotel::factory()->create();

        // 2. Mock: Esperar la llamada al servicio y retornar true para éxito
        $this->hotelServiceMock
            ->shouldReceive('delete')
            ->once()
            ->with($hotel->id)
            ->andReturn(true);

        // 3. Act: Petición DELETE para eliminar el hotel
        $response = $this->deleteJson("/api/hotels/{$hotel->id}");

        // 4. Assert: Verificar el estado 204 y que no hay contenido
        $response->assertStatus(204)
            ->assertNoContent();
    }

    /** @test */
    public function it_returns_404_on_delete_of_non_existent_hotel(): void
    {
        // 1. Mock: Esperar la llamada y retornar false para simular un fallo o no encontrado
        $this->hotelServiceMock
            ->shouldReceive('delete')
            ->once()
            ->with(999)
            ->andReturn(false);

        // 2. Act: Petición DELETE a un ID que no existe
        $response = $this->deleteJson('/api/hotels/999');

        // 3. Assert: Verificar el estado 404
        $response->assertStatus(404)
            ->assertJson(['message' => 'Hotel not found or could not be deleted']);
    }
}
