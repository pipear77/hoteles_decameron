<?php

namespace Tests\Feature;

use App\Models\Accommodation;
use App\Models\Hotel;
use App\Models\HotelRoomConfiguration;
use App\Models\RoomType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class HotelRoomConfigurationControllerTest extends TestCase
{
    // Resetea la base de datos para cada test, asegurando un entorno limpio.
    use RefreshDatabase;
    use WithFaker;

    /**
     * @var User
     */
    protected User $user;
    /**
     * @var Hotel
     */
    protected Hotel $hotel;
    /**
     * @var RoomType
     */
    protected RoomType $roomType;
    /**
     * @var Accommodation
     */
    protected Accommodation $accommodation;
    /**
     * @var HotelRoomConfiguration
     */
    protected HotelRoomConfiguration $configuration;

    /**
     * Configuración inicial para cada test.
     * Crea un usuario y un hotel para simular un entorno de trabajo.
     * Se usa `actingAs` para autenticar al usuario en cada petición.
     */
    protected function setUp(): void
    {
        parent::setUp();

        // Creación de datos de prueba en cada test
        $this->user = User::factory()->create();
        $this->actingAs($this->user);

        // Se crea un hotel con una capacidad total definida.
        $this->hotel = Hotel::factory()->create(['user_id' => $this->user->id, 'rooms_total' => 30]);

        $this->roomType = RoomType::factory()->create();
        $this->accommodation = Accommodation::factory()->create();

        $this->configuration = $this->hotel->roomConfigurations()->create([
            'room_type_id' => $this->roomType->id,
            'accommodation_id' => $this->accommodation->id,
            'quantity' => 10,
        ]);
    }

    /**
     * Prueba que se puede obtener la lista de configuraciones para un hotel.
     */
    public function test_can_get_list_of_configurations(): void
    {
        $response = $this->json('GET', route('hotels.room-configurations.index', $this->hotel));
        $response->assertStatus(200);

        // Verificamos que los datos devueltos contienen los fragmentos de la configuración.
        // La validación ahora debe usar las claves foráneas y los nombres del modelo.
        $response->assertJsonFragment([
            'quantity' => $this->configuration->quantity,
            'room_type_id' => $this->configuration->room_type_id,
            'accommodation_id' => $this->configuration->accommodation_id,
        ]);
    }

    /**
     * Prueba que no se puede obtener la lista de configuraciones de un hotel que no pertenece al usuario.
     */
    public function test_cannot_get_configurations_of_unowned_hotel(): void
    {
        // Creamos un hotel para un usuario diferente.
        $anotherUser = User::factory()->create();
        $anotherHotel = Hotel::factory()->create(['user_id' => $anotherUser->id]);

        $response = $this->json('GET', route('hotels.room-configurations.index', $anotherHotel));

        // Esperamos un error 403 Forbidden.
        $response->assertStatus(403);
    }

    /**
     * Prueba que se puede crear una nueva configuración de habitación.
     */
    public function test_can_create_a_configuration(): void
    {
        // Creación de datos de prueba para este test específico,
        // asegurando que no excedan la capacidad total del hotel.
        $data = [
            'room_type_id' => RoomType::factory()->create()->id,
            'accommodation_id' => Accommodation::factory()->create()->id,
            'quantity' => 15,
        ];

        // Se necesita un hotel con suficiente capacidad para este test.
        // Aseguramos que la suma de las habitaciones existentes y las nuevas no superen el total del hotel.
        $totalRooms = $this->hotel->roomConfigurations->sum('quantity');
        $newQuantity = 15;
        // Asumiendo que el hotel creado en setUp tiene 30 habitaciones totales, 10 ya están usadas.
        // Se puede crear una configuración con 15 habitaciones nuevas (10 + 15 = 25, que es < 30).
        $this->assertLessThanOrEqual($this->hotel->rooms_total - $totalRooms, $newQuantity);

        $response = $this->json('POST', route('hotels.room-configurations.store', $this->hotel), $data);
        $response->assertStatus(201);

        $this->assertDatabaseHas('hotel_room_configurations', [
            'hotel_id' => $this->hotel->id,
            'room_type_id' => $data['room_type_id'],
            'accommodation_id' => $data['accommodation_id'],
            'quantity' => $data['quantity'],
        ]);
    }

    /**
     * Prueba que no se puede crear una configuración si el hotel no pertenece al usuario.
     */
    public function test_cannot_create_a_configuration_if_hotel_is_not_owned(): void
    {
        // Creamos un hotel para un usuario diferente.
        $anotherUser = User::factory()->create();
        $anotherHotel = Hotel::factory()->create(['user_id' => $anotherUser->id]);

        $data = [
            'room_type_id' => $this->roomType->id,
            'accommodation_id' => $this->accommodation->id,
            'quantity' => 15,
        ];

        $response = $this->json('POST', route('hotels.room-configurations.store', $anotherHotel), $data);
        $response->assertStatus(403);
    }

    /**
     * Prueba que no se puede crear una configuración si la cantidad excede la capacidad del hotel.
     */
    public function test_cannot_create_a_configuration_if_hotel_is_full(): void
    {
        // Se crea un hotel y sus dependencias *específicamente para este test*.
        // Esto aísla el test y evita conflictos de unicidad con los datos del setUp().
        $hotel = Hotel::factory()->create(['user_id' => $this->user->id, 'rooms_total' => 10]);
        $roomType = RoomType::factory()->create();
        $accommodation = Accommodation::factory()->create();

        // Se crea una configuración que llena el hotel.
        $hotel->roomConfigurations()->create([
            'room_type_id' => $roomType->id,
            'accommodation_id' => $accommodation->id,
            'quantity' => 10,
        ]);

        // El test verifica si la cantidad es mayor que un valor permitido
        // (por ejemplo, la capacidad total del hotel). Se envía una cantidad
        // absurdamente alta para forzar el fallo de validación.
        $data = [
            'room_type_id' => RoomType::factory()->create()->id,
            'accommodation_id' => Accommodation::factory()->create()->id,
            'quantity' => 1,
        ];

        // Se envía la petición POST al controlador.
        $response = $this->json('POST', route('hotels.room-configurations.store', $hotel), $data);

        // Se verifica que la respuesta tiene un código de estado 422 (Unprocessable Entity).
        $response->assertStatus(422);

        // Se verifica que el error de validación está específicamente en el campo 'quantity'.
        $response->assertJsonValidationErrors(['quantity']);
    }

    /**
     * Prueba que se puede ver una configuración de habitación específica.
     */
    public function test_can_show_a_specific_configuration(): void
    {
        $response = $this->json('GET', route('hotels.room-configurations.show', [
            'hotel' => $this->hotel,
            'room_configuration' => $this->configuration,
        ]));
        $response->assertStatus(200);

        $response->assertJsonFragment([
            'id' => $this->configuration->id,
            'quantity' => $this->configuration->quantity,
            'room_type_id' => $this->configuration->room_type_id,
            'accommodation_id' => $this->configuration->accommodation_id,
        ]);
    }

    /**
     * Prueba que se puede actualizar una configuración de habitación existente.
     */
    public function test_can_update_a_configuration(): void
    {
        $updatedData = [
            'room_type_id' => $this->roomType->id,
            'accommodation_id' => $this->accommodation->id,
            'quantity' => 10,
        ];

        $response = $this->json('PUT', route('hotels.room-configurations.update', [
            'hotel' => $this->hotel,
            'room_configuration' => $this->configuration,
        ]), $updatedData);
        $response->assertStatus(200);

        $this->assertDatabaseHas('hotel_room_configurations', [
            'id' => $this->configuration->id,
            'room_type_id' => $updatedData['room_type_id'],
            'accommodation_id' => $updatedData['accommodation_id'],
            'quantity' => $updatedData['quantity'],
        ]);
    }

    /**
     * Prueba que se puede eliminar una configuración de habitación.
     */
    public function test_can_delete_a_configuration(): void
    {
        $response = $this->json('DELETE', route('hotels.room-configurations.destroy', [
            'hotel' => $this->hotel,
            'room_configuration' => $this->configuration,
        ]));
        $response->assertStatus(204);

        $this->assertDatabaseMissing('hotel_room_configurations', [
            'id' => $this->configuration->id,
        ]);
    }
}
