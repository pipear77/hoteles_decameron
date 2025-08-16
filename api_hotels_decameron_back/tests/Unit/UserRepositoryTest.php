<?php

namespace Tests\Unit;

use App\Models\User;
use App\Models\Role;
use App\Repositories\UserRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\BeforeEach;
use Illuminate\Database\Eloquent\Collection;

/**
 * Clase de test para el repositorio de usuarios.
 * Usamos RefreshDatabase para asegurar que la base de datos esté limpia y lista para cada test.
 * Esto sigue el principio KISS y garantiza la fiabilidad de las pruebas.
 */
class UserRepositoryTest extends TestCase
{
    // Uso de trait moderno para la gestión de la base de datos de test.
    use RefreshDatabase;

    /**
     * @var UserRepository La instancia del repositorio.
     * Esta propiedad se inicializa antes de cada test.
     */
    protected UserRepository $repository;

    /**
     * El método setUp() se ejecuta antes de cada test.
     * Se usa para inicializar las dependencias necesarias.
     * El atributo #[BeforeEach] es el reemplazo moderno de setUp().
     */
    #[BeforeEach]
    public function setUp(): void
    {
        // Es crucial llamar al setUp del padre si se sobreescribe.
        parent::setUp();

        // Se inicializa el repositorio, asegurando que esté disponible para cada test.
        $this->repository = new UserRepository();
    }

    #[Test]
    public function it_can_get_all_users_with_role_relationship(): void
    {
        // GIVEN: Un rol y tres usuarios asociados.
        $role = Role::factory()->create();
        User::factory()->count(3)->create(['role_id' => $role->id]);

        // WHEN: Se llama al método all del repositorio.
        $users = $this->repository->all();

        // THEN: Se obtienen 3 usuarios y la relación está cargada.
        $this->assertInstanceOf(Collection::class, $users);
        $this->assertCount(3, $users);
        $this->assertTrue($users->first()->relationLoaded('role'));
        $this->assertEquals($role->id, $users->first()->role->id);
    }

    #[Test]
    public function it_can_find_a_user_by_id(): void
    {
        // GIVEN: Un usuario existente.
        $user = User::factory()->create();

        // WHEN: Se busca el usuario por su ID.
        $foundUser = $this->repository->find($user->id);

        // THEN: Se encuentra el usuario correcto.
        $this->assertNotNull($foundUser);
        $this->assertEquals($user->id, $foundUser->id);
    }

    #[Test]
    public function it_can_find_a_user_by_email(): void
    {
        // GIVEN: Un usuario existente.
        $user = User::factory()->create();

        // WHEN: Se busca el usuario por su email.
        $foundUser = $this->repository->findByEmail($user->email);

        // THEN: Se encuentra el usuario correcto.
        $this->assertNotNull($foundUser);
        $this->assertEquals($user->email, $foundUser->email);
    }

    #[Test]
    public function it_can_create_a_user(): void
    {
        // GIVEN: Un array de datos válidos para un nuevo usuario, incluyendo la contraseña.
        // Esto hace que el test sea explícito y no dependa del comportamiento del factory.
        $userData = [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john.doe@example.com',
            'password' => 'password123', // La contraseña se hashea dentro del repositorio
            'role_id' => Role::factory()->create()->id, // Crea un rol y asocia su ID
        ];

        // WHEN: Se llama al método create del repositorio con los datos.
        $createdUser = $this->repository->create($userData);

        // THEN: El usuario es creado y existe en la base de datos con los datos correctos.
        $this->assertNotNull($createdUser);
        $this->assertEquals($userData['first_name'], $createdUser->first_name);
        $this->assertDatabaseHas('users', ['email' => $userData['email']]);
    }

    #[Test]
    public function it_can_update_a_user(): void
    {
        // GIVEN: Un usuario existente y nuevos datos.
        $user = User::factory()->create();
        $newFirstName = 'Jane';

        // WHEN: Se actualiza el usuario.
        $updatedUser = $this->repository->update($user->id, ['first_name' => $newFirstName]);

        // THEN: El usuario se actualiza correctamente en la base de datos.
        $this->assertNotNull($updatedUser);
        $this->assertEquals($newFirstName, $updatedUser->first_name);
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'first_name' => $newFirstName,
        ]);
    }

    #[Test]
    public function it_can_delete_a_user(): void
    {
        // GIVEN: Un usuario existente.
        $user = User::factory()->create();

        // WHEN: Se elimina el usuario.
        $isDeleted = $this->repository->delete($user->id);

        // THEN: El usuario es eliminado de la base de datos.
        $this->assertTrue($isDeleted);
        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    }
}
