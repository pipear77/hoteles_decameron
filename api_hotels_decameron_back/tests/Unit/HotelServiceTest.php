<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Services\HotelService;
use App\Models\User;
use App\Models\Hotel;
use App\Models\RoomType;
use App\Models\Accommodation;
use App\Models\City;

class HotelServiceTest extends TestCase
{
    use RefreshDatabase;

    protected HotelService $hotelService;
    protected User $user;
    protected City $city;
    protected RoomType $roomType;
    protected Accommodation $accommodation;

    protected function setUp(): void
    {
        parent::setUp();

        // Crear los registros mínimos que exigen las FKs y la lógica de negocio
        $this->user = User::factory()->create();
        $this->city = City::factory()->create();

        // Crear room type y accommodation con los nombres que usa el service
        $this->roomType = RoomType::factory()->create(['name' => 'ESTANDAR']);
        $this->accommodation = Accommodation::factory()->create(['name' => 'SENCILLA']);

        // Instancia el service real (usa Eloquent internamente)
        $this->hotelService = app(HotelService::class);
    }

    /** @test */
    public function it_creates_a_new_hotel_and_its_room_configurations(): void
    {
        $hotelData = [
            'name' => 'Nuevo Hotel',
            'address' => '123 Main St',
            'nit' => '1234567890',
            'rooms_total' => 10,
            'city_id' => $this->city->id,
            'user_id' => $this->user->id,
            'room_configurations' => [
                // create() espera nombres en strings y los resuelve a ids
                ['room_type' => 'ESTANDAR', 'accommodation' => 'SENCILLA', 'quantity' => 10],
            ],
        ];

        $hotel = $this->hotelService->create($hotelData);

        $this->assertInstanceOf(Hotel::class, $hotel);

        // Comprueba que el registro se creó en la BD
        $this->assertDatabaseHas('hotels', [
            'id' => $hotel->id,
            'name' => 'Nuevo Hotel',
            'nit' => '1234567890',
            'user_id' => $this->user->id,
            'city_id' => $this->city->id,
        ]);

        // Comprueba que la configuración de habitación se creó
        $this->assertDatabaseHas('hotel_room_configurations', [
            'hotel_id' => $hotel->id,
            'quantity' => 10,
        ]);
    }

    /** @test */
    public function it_updates_an_existing_hotel_and_its_room_configurations(): void
    {
        // Crear hotel existente (vía factory) ligado al user y a la ciudad
        $hotel = Hotel::factory()->create([
            'user_id' => $this->user->id,
            'city_id' => $this->city->id,
            'rooms_total' => 2,
        ]);

        // Crear otra acomodación/room type si quieres usar distintos ids en el update
        $rt = RoomType::factory()->create(['name' => 'SUITE']);
        $acc = Accommodation::factory()->create(['name' => 'DOBLE']);

        $updatedData = [
            'name' => 'Hotel Actualizado',
            'address' => '456 Oak St',
            'nit' => '2711977740',
            'rooms_total' => 5,
            'city_id' => $this->city->id,
            'user_id' => $this->user->id,
            // update() espera ids para room_configurations
            'room_configurations' => [
                [
                    'room_type_id' => $rt->id,
                    'accommodation_id' => $acc->id,
                    'quantity' => 5
                ]
            ]
        ];

        $result = $this->hotelService->update($hotel->id, $updatedData);

        $this->assertNotNull($result);
        $this->assertInstanceOf(Hotel::class, $result);
        $this->assertEquals('Hotel Actualizado', $result->name);

        // Aseguramos que la tabla de configuraciones ahora tenga el nuevo registro
        $this->assertDatabaseHas('hotel_room_configurations', [
            'hotel_id' => $hotel->id,
            'room_type_id' => $rt->id,
            'accommodation_id' => $acc->id,
            'quantity' => 5,
        ]);
    }

    /** @test */
    public function it_returns_null_when_updating_a_non_existent_hotel(): void
    {
        $result = $this->hotelService->update(999999, [
            'name' => 'Non Existent Hotel',
            'user_id' => $this->user->id,
        ]);

        $this->assertNull($result);
    }

    /** @test */
    public function it_deletes_a_hotel(): void
    {
        $hotel = Hotel::factory()->create(['user_id' => $this->user->id, 'city_id' => $this->city->id]);

        $deleted = $this->hotelService->delete($hotel->id);

        $this->assertTrue($deleted);
        $this->assertDatabaseMissing('hotels', ['id' => $hotel->id]);
    }

    /** @test */
    public function it_returns_false_when_deleting_a_non_existent_hotel(): void
    {
        $deleted = $this->hotelService->delete(999999);
        $this->assertFalse($deleted);
    }
}
