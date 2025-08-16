<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Role;
use App\Models\User;
use App\Services\UserService;
use App\Repositories\UserRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Mockery;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

/**
 * Pruebas Unitarias para la clase UserService.
 */
class UserServiceTest extends TestCase
{
    /**
     * @var Mockery\MockInterface|UserRepositoryInterface
     */
    protected $userRepositoryMock;

    /**
     * @var UserService
     */
    protected $userService;

    /**
     * Configuración inicial para cada prueba.
     */
    public function setUp(): void
    {
        parent::setUp();
        // Creamos un mock del UserRepositoryInterface.
        $this->userRepositoryMock = Mockery::mock(UserRepositoryInterface::class);

        // Inicializamos el UserService con el mock del repositorio como su dependencia.
        $this->userService = new UserService($this->userRepositoryMock);
    }

    /**
     * Limpia los mocks después de cada prueba.
     */
    public function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    /**
     * Prueba que getAllUsers llama al método 'all' del repositorio.
     */
    public function test_it_can_get_all_users()
    {
        // Arrange
        $this->userRepositoryMock->shouldReceive('all')
            ->once()
            ->andReturn(new Collection());

        // Act
        $users = $this->userService->getAllUsers();

        // Assert
        $this->assertInstanceOf(Collection::class, $users);
    }

    /**
     * Prueba que registerUser llama al método 'create' del repositorio.
     */
    public function test_it_can_register_a_user()
    {
        // Arrange
        $userData = [
            'name' => 'John Doe',
            'email' => 'john.doe@example.com',
            'password' => 'password123',
        ];

        // Mock de la llamada estática para el rol.
        $roleStub = (object) ['id' => 1, 'name' => 'user'];

        // Se usa 'overload' en lugar de 'alias' para evitar el conflicto de re-declaración.
        // Se corrige el método mockeado de 'firstOrFail' a 'first' para que coincida con el servicio.
        Mockery::mock('overload:App\Models\Role')
            ->shouldReceive('where')
            ->with('name', 'user')
            ->andReturnSelf()
            ->shouldReceive('first')
            ->andReturn($roleStub);

        // Mock de la fachada Hash.
        Hash::shouldReceive('make')
            ->once()
            ->with($userData['password'])
            ->andReturn('hashed_password');

        // Expectativa de llamada al repositorio.
        $this->userRepositoryMock->shouldReceive('create')
            ->once()
            ->with(Mockery::subset([
                'name' => 'John Doe',
                'email' => 'john.doe@example.com',
                'password' => 'hashed_password',
                'role_id' => 1,
            ]))->andReturn(new User());

        // Act
        $user = $this->userService->registerUser($userData);

        // Assert
        $this->assertInstanceOf(User::class, $user);
    }

    /**
     * Prueba que authenticateUser lanza una excepción si las credenciales son incorrectas.
     */
    public function test_it_throws_exception_on_wrong_credentials()
    {
        // Arrange
        $credentials = ['email' => 'test@example.com', 'password' => 'wrong_password'];
        $user = new User(['email' => 'test@example.com', 'password' => 'hashed_password']);

        $this->userRepositoryMock->shouldReceive('findByEmail')
            ->once()
            ->with($credentials['email'])
            ->andReturn($user);

        Hash::shouldReceive('check')
            ->once()
            ->andReturn(false);

        // Assert & Act
        $this->expectException(ValidationException::class);
        $this->userService->authenticateUser($credentials);
    }

    /**
     * Prueba que updateUser llama al método 'update' del repositorio y hashea la contraseña.
     */
    public function test_it_can_update_a_user_with_password()
    {
        // Arrange
        $userId = 1;
        $userData = ['password' => 'new_password'];

        Hash::shouldReceive('make')
            ->once()
            ->with($userData['password'])
            ->andReturn('new_hashed_password');

        $this->userRepositoryMock->shouldReceive('update')
            ->once()
            ->with($userId, ['password' => 'new_hashed_password'])
            ->andReturn(new User());

        // Act
        $user = $this->userService->updateUser($userId, $userData);

        // Assert
        $this->assertInstanceOf(User::class, $user);
    }

    /**
     * Prueba que deleteUser llama al método 'delete' del repositorio.
     */
    public function test_it_can_delete_a_user()
    {
        // Arrange
        $userId = 1;
        $this->userRepositoryMock->shouldReceive('delete')
            ->once()
            ->with($userId)
            ->andReturn(true);

        // Act
        $result = $this->userService->deleteUser($userId);

        // Assert
        $this->assertTrue($result);
    }

    /**
     * Prueba que updateUserRole lanza una excepción si el usuario no existe.
     */
    public function test_it_throws_exception_if_user_not_found()
    {
        // Arrange
        $userId = 999;
        $newRoleId = 2;

        $this->userRepositoryMock->shouldReceive('find')
            ->once()
            ->with($userId)
            ->andReturn(null);

        // Mock de la llamada estática para el rol.
        Mockery::mock('overload:App\Models\Role')
            ->shouldReceive('find')
            ->andReturn(new Role(['id' => $newRoleId]));

        // Assert & Act
        $this->expectException(NotFoundHttpException::class);
        $this->userService->updateUserRole($userId, $newRoleId);
    }
}
