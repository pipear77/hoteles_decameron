<?php
// src/tests/Feature/AuthControllerTest.php

namespace Tests\Feature;

use App\Models\User;
use App\Services\UserServiceInterface;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Str;
use Mockery;
use Tests\TestCase;

/**
 * Clase de pruebas unitarias para AuthController.
 *
 * Esta clase se enfoca en probar el comportamiento del controlador de autenticación,
 * aislando su lógica de la capa de servicio y la base de datos mediante mocks.
 */
class AuthControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * @var \Mockery\MockInterface|UserServiceInterface
     */
    protected $userServiceMock;

    /**
     * Configuración inicial para cada prueba.
     * Creamos un mock del servicio y lo registramos en el contenedor de servicios
     * para asegurar que las pruebas no interactúen con la base de datos real.
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->userServiceMock = Mockery::mock(UserServiceInterface::class);
        $this->app->instance(UserServiceInterface::class, $this->userServiceMock);
    }

    /**
     * Limpia los mocks después de cada prueba.
     */
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    // --- Métodos de ayuda para DRY y mantener la claridad ---

    /**
     * Define los datos de login comunes para las pruebas.
     *
     * @return array
     */
    private function getLoginCredentials(): array
    {
        return [
            'email'    => $this->faker->unique()->safeEmail,
            'password' => 'password123',
        ];
    }

    // --- Pruebas de autenticación ---

    /**
     * @test
     * Prueba que un usuario se puede autenticar exitosamente.
     */
    public function it_can_authenticate_a_user_successfully(): void
    {
        // ARRANGE
        $credentials = $this->getLoginCredentials();
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

        // ACT
        $response = $this->postJson('/api/login', $credentials);

        // ASSERT
        $response->assertStatus(200)->assertJson($result);
    }

    /**
     * @test
     * Prueba que se retorna un error 401 para credenciales de login inválidas.
     */
    public function it_returns_401_for_invalid_login_credentials(): void
    {
        // ARRANGE
        $credentials = $this->getLoginCredentials();
        $result = [
            'status'  => false,
            'message' => 'Credenciales inválidas.',
        ];

        // Configuramos el mock para que devuelva una respuesta fallida.
        // Simulamos que el servicio lanza una excepción para las credenciales inválidas.
        $this->userServiceMock
            ->shouldReceive('authenticateUser')
            ->once()
            ->with($credentials)
            ->andThrow(\Exception::class, 'Credenciales incorrectas');

        // ACT
        $response = $this->postJson('/api/login', $credentials);

        // ASSERT
        $response->assertStatus(401)->assertJson($result);
    }

    // --- Pruebas de cierre de sesión ---

    /**
     * @test
     * Prueba que un usuario autenticado puede cerrar sesión exitosamente.
     */
    public function it_can_logout_a_user_successfully(): void
    {
        // ARRANGE: Se crea un mock que implementa la interfaz Authenticatable.
        $mockUser = Mockery::mock(Authenticatable::class);

        // Se configura el mock del usuario para que los métodos necesarios sean llamados por actingAs.
        $mockUser->shouldReceive('getAuthIdentifierName')->andReturn('id');
        $mockUser->shouldReceive('getAuthIdentifier')->andReturn(1);

        // Se delega la autenticación a Laravel para no tener conflictos con Mockery.
        $this->actingAs($mockUser, 'sanctum');

        // Se configura el mock del servicio para que el método 'logoutUser' sea llamado.
        $this->userServiceMock
            ->shouldReceive('logoutUser')
            ->once()
            ->andReturn(true);

        // ACT
        $response = $this->postJson('/api/logout');

        // ASSERT
        $response->assertStatus(200)
            ->assertJson(['message' => 'Logout exitoso']);
    }
}
