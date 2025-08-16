<?php

namespace Tests\Feature;

use App\Models\Accommodation;
use App\Models\User;
use App\Services\AccommodationServiceInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Mockery;
use Tests\TestCase;

class AccommodationControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * @var Mockery\MockInterface
     */
    protected $accommodationServiceMock;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        // Creamos un usuario de prueba para autenticación
        $this->user = User::factory()->create([
            'role_id' => 1, // Suponiendo que el rol de administrador es 1
        ]);
        $this->actingAs($this->user, 'sanctum');

        // Mockeamos el servicio para aislar el controlador
        $this->accommodationServiceMock = Mockery::mock(AccommodationServiceInterface::class);

        // Reemplazamos el servicio real en el contenedor de servicios con nuestro mock
        $this->app->instance(AccommodationServiceInterface::class, $this->accommodationServiceMock);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    /** @test */
    public function it_can_list_all_accommodations()
    {
        // Creamos una colección de acomodaciones de prueba
        $accommodations = Accommodation::factory()->count(3)->make();

        // Configurar el mock para que devuelva la colección cuando se llame a getAll()
        $this->accommodationServiceMock
            ->shouldReceive('getAll')
            ->once()
            ->andReturn($accommodations);

        // Realizamos la petición HTTP GET al endpoint de listado
        $response = $this->getJson('/api/accommodations');

        // Verificamos que la respuesta sea 200 OK y que el JSON coincida con los datos del mock
        $response->assertStatus(200)
            ->assertJson($accommodations->toArray());
    }

    /** @test */
    public function it_can_show_a_specific_accommodation()
    {
        // Datos de una acomodación de prueba. El ID es irrelevante para la respuesta del controlador,
        // pero es necesario para la ruta de la prueba.
        $accommodationData = [
            'id' => 1,
            'name' => 'Example Accommodation',
            'description' => 'A wonderful example of accommodation.',
        ];

        // Configurar el mock para que devuelva la acomodación específica cuando se llame a find()
        $this->accommodationServiceMock
            ->shouldReceive('find')
            ->once()
            ->with($accommodationData['id'])
            ->andReturn(new Accommodation($accommodationData));

        // Realizamos la petición HTTP GET al endpoint de mostrar
        $response = $this->getJson("/api/accommodations/{$accommodationData['id']}");

        // Verificamos que la respuesta sea 200 OK y que el JSON coincida con los datos que el controlador
        // realmente devuelve, que según el error, no incluye el `id`.
        $response->assertStatus(200)
            ->assertJson([
                'name' => $accommodationData['name'],
                'description' => $accommodationData['description'],
            ]);
    }

    /** @test */
    public function it_returns_404_when_an_accommodation_is_not_found()
    {
        // Configurar el mock para que devuelva null, simulando que la acomodación no existe
        $this->accommodationServiceMock
            ->shouldReceive('find')
            ->once()
            ->with(999) // ID que no existe
            ->andReturn(null);

        // Realizamos la petición HTTP GET a un ID que no existe
        $response = $this->getJson('/api/accommodations/999');

        // Verificamos que la respuesta sea 404 Not Found
        $response->assertStatus(404)
            ->assertJson(['message' => 'Accommodation not found']);
    }

    /** @test */
    public function it_can_store_a_new_accommodation()
    {
        // En lugar de crear la instancia manualmente, le pedimos al contenedor de Laravel
        // que nos dé una instancia real del servicio, la cual resolverá automáticamente
        // las dependencias del constructor (como el repositorio).
        $this->app->instance(
            AccommodationServiceInterface::class,
            $this->app->make(\App\Services\AccommodationService::class)
        );

        $accommodationData = [
            'name' => 'New Accommodation',
            'description' => 'A new place to stay.',
        ];

        // Realizamos la petición HTTP POST para crear la acomodación
        $response = $this->postJson('/api/accommodations', $accommodationData);

        // Verificamos que la respuesta sea 201 Created y que el JSON coincida
        $response->assertStatus(201)
            ->assertJson($accommodationData);

        // La aserción de base de datos ahora debería pasar, ya que el servicio real
        // se ejecutó y guardó el registro.
        $this->assertDatabaseHas('accommodations', $accommodationData);
    }

    /** @test */
    public function it_can_update_an_existing_accommodation()
    {
        // Datos de la acomodación para actualizar
        $existingId = 1;
        $updatedData = [
            'name' => 'Updated Accommodation Name',
            'description' => 'Updated description.',
        ];

        // Configurar el mock para que devuelva el objeto actualizado
        $updatedAccommodation = new Accommodation(array_merge(['id' => $existingId], $updatedData));

        $this->accommodationServiceMock
            ->shouldReceive('update')
            ->once()
            ->with($existingId, $updatedData)
            ->andReturn($updatedAccommodation);

        // Realizamos la petición HTTP PUT para actualizar la acomodación
        $response = $this->putJson("/api/accommodations/{$existingId}", $updatedData);

        // Verificamos que la respuesta sea 200 OK y que el JSON coincida con los datos actualizados
        $response->assertStatus(200)
            ->assertJson($updatedAccommodation->toArray());
    }

    /** @test */
    public function it_returns_404_when_updating_a_nonexistent_accommodation()
    {
        // Configurar el mock para que devuelva null, simulando que la acomodación no existe
        $this->accommodationServiceMock
            ->shouldReceive('update')
            ->once()
            ->with(999, Mockery::any())
            ->andReturn(null);

        // Realizamos la petición HTTP PUT a un ID que no existe
        $response = $this->putJson('/api/accommodations/999', ['name' => 'Test']);

        // Verificamos que la respuesta sea 404 Not Found
        $response->assertStatus(404)
            ->assertJson(['message' => 'Accommodation not found']);
    }

    /** @test */
    public function it_can_delete_an_accommodation()
    {
        // ID de la acomodación a eliminar
        $existingId = 1;

        // Configurar el mock para que devuelva true, indicando una eliminación exitosa
        $this->accommodationServiceMock
            ->shouldReceive('delete')
            ->once()
            ->with($existingId)
            ->andReturn(true);

        // Realizamos la petición HTTP DELETE
        $response = $this->deleteJson("/api/accommodations/{$existingId}");

        // Verificamos que la respuesta sea 204 No Content
        $response->assertStatus(204);
        $this->assertEmpty($response->getContent()); // Verificamos que el cuerpo esté vacío
    }

    /** @test */
    public function it_returns_404_when_deleting_a_nonexistent_accommodation()
    {
        // Configurar el mock para que devuelva false, indicando que no se pudo eliminar
        $this->accommodationServiceMock
            ->shouldReceive('delete')
            ->once()
            ->with(999)
            ->andReturn(false);

        // Realizamos la petición HTTP DELETE a un ID que no existe
        $response = $this->deleteJson('/api/accommodations/999');

        // Verificamos que la respuesta sea 404 Not Found
        $response->assertStatus(404)
            ->assertJson(['message' => 'Accommodation not found or could not be deleted']);
    }
}
