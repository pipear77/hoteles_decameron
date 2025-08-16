<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use App\Services\UserServiceInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Str;
use Laravel\Sanctum\Sanctum;
use Mockery;
use Tests\TestCase;

/**
 * Clase de pruebas unitarias para AuthController.
 * Esta clase se enfoca en probar el comportamiento del controlador de autenticación,
 * aislando su lógica de la capa de servicio y la base de datos mediante mocks.
 */
class AuthControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * @var \Mockery\MockInterface
     */
    protected $userServiceMock;

    protected function setUp(): void
    {
        parent::setUp();

        // Creamos un mock del servicio y lo registramos en el contenedor de servicios
        // para asegurar que las pruebas no interactúen con la base de datos real.
        $this->userServiceMock = Mockery::mock(UserServiceInterface::class);
        $this->app->instance(UserServiceInterface::class, $this->userServiceMock);
    }

    protected function tearDown(): void
    {
        // Cierra los mocks después de cada prueba para evitar fugas de memoria.
        Mockery::close();
        parent::tearDown();
    }

    /** @test */
    public function it_can_register_a_new_user_successfully(): void
    {
        // Creamos un rol real en la base de datos de prueba para que la validación
        // de `role_id` no falle.
        $role = Role::factory()->create();

        // Datos de prueba para simular la petición de registro.
        $userData = [
            'first_name'            => 'John',
            'last_name'             => 'Doe',
            'email'                 => 'john@example.com',
            'password'              => 'password',
            'password_confirmation' => 'password',
            'role_id'               => $role->id, // Usamos el ID del rol creado.
        ];

        // Configuramos el mock para que cuando se llame a registerUser(),
        // retorne un objeto User simulado. Esto evita la interacción real con la DB.
        $this->userServiceMock
            ->shouldReceive('registerUser')
            ->once()
            ->andReturn(
                new User([
                    'id'         => 1,
                    'first_name' => $userData['first_name'],
                    'last_name'  => $userData['last_name'],
                    'email'      => $userData['email'],
                    'role_id'    => $userData['role_id'],
                ])
            );

        // Realizamos la petición HTTP POST al endpoint de registro.
        $response = $this->postJson('/api/register', $userData);

        // Verificamos el código de estado y la estructura de la respuesta JSON.
        $response->assertStatus(201)
            ->assertJson([
                'status'  => true,
                'message' => '¡Usuario creado exitosamente!',
                'user'    => [
                    'email'      => 'john@example.com',
                    'first_name' => 'John',
                    'last_name'  => 'Doe',
                ],
            ]);
    }

    /** @test */
    public function it_returns_a_500_error_when_registration_fails(): void
    {
        // Creamos un rol real para evitar que la validación de `role_id` falle.
        $role = Role::factory()->create();

        $userData = [
            'first_name'            => $this->faker->firstName,
            'last_name'             => $this->faker->lastName,
            'email'                 => $this->faker->unique()->safeEmail,
            'password'              => 'password123',
            'password_confirmation' => 'password123',
            'role_id'               => $role->id,
        ];

        // Configuramos el mock para que lance una excepción.
        // Esto simula un fallo en la capa de servicio.
        $this->userServiceMock
            ->shouldReceive('registerUser')
            ->once()
            ->andThrow(new \Exception('Database error'));

        $response = $this->postJson('/api/register', $userData);

        $response->assertStatus(500)
            ->assertJson([
                'status'  => false,
                'message' => 'Hubo un error al crear el usuario.',
                'error'   => 'Database error',
            ]);
    }

    /** @test */
    public function it_can_authenticate_a_user_successfully(): void
    {
        $credentials = [
            'email'    => $this->faker->unique()->safeEmail,
            'password' => 'password123',
        ];

        $result = [
            'status'  => true,
            'message' => '¡Autenticación exitosa!',
            'token'   => Str::random(60),
            'user'    => [
                'id'    => 1,
                'email' => $credentials['email'],
            ],
        ];

        // Configuramos el mock para que devuelva una respuesta exitosa.
        $this->userServiceMock
            ->shouldReceive('authenticateUser')
            ->once()
            ->with($credentials)
            ->andReturn($result);

        $response = $this->postJson('/api/login', $credentials);

        $response->assertStatus(200)
            ->assertJson($result);
    }

    /** @test */
    public function it_returns_401_for_invalid_login_credentials(): void
    {
        $credentials = [
            'email'    => $this->faker->unique()->safeEmail,
            'password' => 'wrong-password',
        ];

        $result = [
            'status'  => false,
            'message' => 'Credenciales inválidas.',
        ];

        // Configuramos el mock para que devuelva una respuesta fallida.
        $this->userServiceMock
            ->shouldReceive('authenticateUser')
            ->once()
            ->with($credentials)
            ->andReturn($result);

        $response = $this->postJson('/api/login', $credentials);

        $response->assertStatus(401)
            ->assertJson($result);
    }

    /** @test */
    public function it_can_logout_a_user_successfully(): void
    {
        // Creamos un usuario mock que simulamos que está autenticado.
        // No creamos un usuario real en la base de datos.
        $user = Mockery::mock(User::class);
        $user->shouldReceive('currentAccessToken->delete')->once();

        // Usamos actingAs para simular que el usuario está autenticado.
        $this->actingAs($user, 'sanctum');

        $response = $this->postJson('/api/logout');

        $response->assertStatus(200)
            ->assertJson(['message' => 'Logout exitoso']);
    }
}
