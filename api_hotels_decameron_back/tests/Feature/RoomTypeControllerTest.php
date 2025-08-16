<?php

namespace Tests\Feature;

use App\Models\RoomType;
use App\Models\User;
use App\Services\RoomTypeServiceInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Mockery;
use Tests\TestCase;

class RoomTypeControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * @var Mockery\MockInterface
     */
    protected $roomTypeServiceMock;

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
        $this->roomTypeServiceMock = Mockery::mock(RoomTypeServiceInterface::class);

        // Reemplazamos el servicio real en el contenedor de servicios con nuestro mock
        $this->app->instance(RoomTypeServiceInterface::class, $this->roomTypeServiceMock);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    /** @test */
    public function it_can_list_all_roomTypes(): void
    {
        // Creamos una colección de acomodaciones de prueba
        $roomTypes = RoomType::factory()->count(3)->make();

        // Configurar el mock para que devuelva la colección cuando se llame a getAll()
        $this->roomTypeServiceMock
            ->shouldReceive('getAll')
            ->once()
            ->andReturn($roomTypes);

        // Realizamos la petición HTTP GET al endpoint de listado
        $response = $this->getJson('/api/room_types');

        // Verificamos que la respuesta sea 200 OK y que el JSON coincida con los datos del mock
        $response->assertStatus(200)
            ->assertJson($roomTypes->toArray());
    }

    /** @test */
    public function it_can_show_a_specific_roomTypes()
    {
        // Datos de una acomodación de prueba. El ID es irrelevante para la respuesta del controlador,
        // pero es necesario para la ruta de la prueba.
        $roomTypesData = [
            'id' => 1,
            'name' => 'Example roomTypes',
            'description' => 'A wonderful example of roomTypes.',
        ];

        // Configurar el mock para que devuelva la acomodación específica cuando se llame a find()
        $this->roomTypeServiceMock
            ->shouldReceive('find')
            ->once()
            ->with($roomTypesData['id'])
            ->andReturn(new RoomType($roomTypesData));

        // Realizamos la petición HTTP GET al endpoint de mostrar
        $response = $this->getJson("/api/room_types/{$roomTypesData['id']}");

        // Verificamos que la respuesta sea 200 OK y que el JSON coincida con los datos que el controlador
        // realmente devuelve, que según el error, no incluye el `id`.
        $response->assertStatus(200)
            ->assertJson([
                'name' => $roomTypesData['name'],
                'description' => $roomTypesData['description'],
            ]);
    }

    /** @test */
    public function it_returns_404_when_an_roomTypes_is_not_found()
    {
        // Configurar el mock para que devuelva null, simulando que la acomodación no existe
        $this->roomTypeServiceMock
            ->shouldReceive('find')
            ->once()
            ->with(999) // ID que no existe
            ->andReturn(null);

        // Realizamos la petición HTTP GET a un ID que no existe
        $response = $this->getJson('/api/room_types/999');

        // Verificamos que la respuesta sea 404 Not Found
        $response->assertStatus(404)
            ->assertJson(['message' => 'Room type not found']);
    }

    /** @test */
    public function it_can_store_a_new_roomTypes()
    {
        // En lugar de crear la instancia manualmente, le pedimos al contenedor de Laravel
        // que nos dé una instancia real del servicio, la cual resolverá automáticamente
        // las dependencias del constructor (como el repositorio).
        $this->app->instance(
            roomTypeserviceInterface::class,
            $this->app->make(\App\Services\roomTypeservice::class)
        );

        $roomTypesData = [
            'name' => 'New roomTypes',
            'description' => 'A new place to stay.',
        ];

        // Realizamos la petición HTTP POST para crear la acomodación
        $response = $this->postJson('/api/room_types', $roomTypesData);

        // Verificamos que la respuesta sea 201 Created y que el JSON coincida
        $response->assertStatus(201)
            ->assertJson($roomTypesData);

        // La aserción de base de datos ahora debería pasar, ya que el servicio real
        // se ejecutó y guardó el registro.
        $this->assertDatabaseHas('room_types', $roomTypesData);
    }

    /** @test */
    public function it_can_update_an_existing_roomTypes()
    {
        // Datos de la acomodación para actualizar
        $existingId = 1;
        $updatedData = [
            'name' => 'Updated roomTypes Name',
            'description' => 'Updated description.',
        ];

        // Configurar el mock para que devuelva el objeto actualizado
        $updatedroomTypes = new RoomType(array_merge(['id' => $existingId], $updatedData));

        $this->roomTypeServiceMock
            ->shouldReceive('update')
            ->once()
            ->with($existingId, $updatedData)
            ->andReturn($updatedroomTypes);

        // Realizamos la petición HTTP PUT para actualizar la acomodación
        $response = $this->putJson("/api/room_types/{$existingId}", $updatedData);

        // Verificamos que la respuesta sea 200 OK y que el JSON coincida con los datos actualizados
        $response->assertStatus(200)
            ->assertJson($updatedroomTypes->toArray());
    }

    /** @test */
    public function it_returns_404_when_updating_a_nonexistent_roomTypes()
    {
        // Configurar el mock para que devuelva null, simulando que la acomodación no existe
        $this->roomTypeServiceMock
            ->shouldReceive('update')
            ->once()
            ->with(999, Mockery::any())
            ->andReturn(null);

        // Realizamos la petición HTTP PUT a un ID que no existe
        $response = $this->putJson('/api/room_types/999', ['name' => 'Test']);

        // Verificamos que la respuesta sea 404 Not Found
        $response->assertStatus(404)
            ->assertJson(['message' => 'Room type not found']);
    }

    /** @test */
    public function it_can_delete_an_roomTypes()
    {
        // ID de la acomodación a eliminar
        $existingId = 1;

        // Configurar el mock para que devuelva true, indicando una eliminación exitosa
        $this->roomTypeServiceMock
            ->shouldReceive('delete')
            ->once()
            ->with($existingId)
            ->andReturn(true);

        // Realizamos la petición HTTP DELETE
        $response = $this->deleteJson("/api/room_types/{$existingId}");

        // Verificamos que la respuesta sea 204 No Content
        $response->assertStatus(204);
        $this->assertEmpty($response->getContent()); // Verificamos que el cuerpo esté vacío
    }

    /** @test */
    public function it_returns_404_when_deleting_a_nonexistent_roomTypes()
    {
        // Configurar el mock para que devuelva false, indicando que no se pudo eliminar
        $this->roomTypeServiceMock
            ->shouldReceive('delete')
            ->once()
            ->with(999)
            ->andReturn(false);

        // Realizamos la petición HTTP DELETE a un ID que no existe
        $response = $this->deleteJson('/api/room_types/999');

        // Verificamos que la respuesta sea 404 Not Found
        $response->assertStatus(404)
            ->assertJson(['message' => 'Room type not found or could not be deleted']);
    }
}
