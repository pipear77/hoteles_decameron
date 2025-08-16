<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use App\Services\UserServiceInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;
use Mockery;
use Tests\TestCase;

/**
 * Clase de pruebas para UserController.
 * Utiliza mocks para aislar el controlador del servicio.
 */
class UserControllerTest extends TestCase
{
    // Limpia la base de datos de prueba entre cada test.
    use RefreshDatabase;
    use WithFaker;

    /**
     * @var Mockery\MockInterface
     */
    protected $userServiceMock;

    protected function setUp(): void
    {
        parent::setUp();
        // Vincula una instancia de Mockery del servicio al contenedor de Laravel.
        $this->userServiceMock = Mockery::mock(UserServiceInterface::class);
        $this->app->instance(UserServiceInterface::class, $this->userServiceMock);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    /** @test */
    public function it_can_get_a_list_of_all_users(): void
    {
        // Usa `create()` para persistir los modelos en la base de datos de prueba.
        $users = User::factory()->count(2)->create();

        $this->userServiceMock
            ->shouldReceive('getAllUsers')
            ->once()
            ->andReturn($users);

        Sanctum::actingAs(User::factory()->create());

        $response = $this->getJson('/api/users');

        $response->assertStatus(200);

        // Extraemos solo los datos que nos interesan para evitar problemas de serialización de fechas.
        $expectedJson = $users->map(function ($user) {
            return $user->only(['id', 'first_name', 'last_name', 'email', 'role_id']);
        })->toArray();

        $response->assertJson($expectedJson);
    }

    /** @test */
    public function it_returns_a_500_error_when_getting_all_users_fails(): void
    {
        $this->userServiceMock
            ->shouldReceive('getAllUsers')
            ->once()
            ->andThrow(new \Exception('Database connection failed.'));

        Sanctum::actingAs(User::factory()->create());

        $response = $this->getJson('/api/users');

        $response->assertStatus(500)
            ->assertJson(['message' => 'No se pudo obtener la lista de usuarios.']);
    }

    /** @test */
    public function it_can_show_a_specific_user(): void
    {
        $user = User::factory()->create();

        $this->userServiceMock
            ->shouldReceive('findUserById')
            ->once()
            ->with($user->id)
            ->andReturn($user);

        Sanctum::actingAs(User::factory()->create());

        $response = $this->getJson("/api/users/{$user->id}");

        $response->assertStatus(200);

        // Aseguramos que los datos principales del usuario estén presentes.
        $response->assertJsonFragment([
            'id' => $user->id,
            'first_name' => $user->first_name,
            'email' => $user->email,
        ]);
    }

    /** @test */
    public function it_returns_404_when_showing_a_non_existent_user(): void
    {
        $this->userServiceMock
            ->shouldReceive('findUserById')
            ->once()
            ->with(999)
            ->andReturn(null);

        Sanctum::actingAs(User::factory()->create());

        $response = $this->getJson('/api/users/999');

        $response->assertStatus(404)
            ->assertJson(['message' => 'Usuario no encontrado.']);
    }
}
