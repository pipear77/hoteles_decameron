<?php

namespace Tests\Feature;

use App\Models\Hotel;
use App\Models\User;
use App\Models\City;
use App\Services\HotelServiceInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Mockery;
use Tests\TestCase;

/**
 * Clase de pruebas para HotelController.
 */
class HotelControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * @var Mockery\MockInterface
     */
    protected $hotelServiceMock;

    protected function setUp(): void
    {
        parent::setUp();
        $this->hotelServiceMock = Mockery::mock(HotelServiceInterface::class);
        $this->app->instance(HotelServiceInterface::class, $this->hotelServiceMock);
        Sanctum::actingAs(User::factory()->create());
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    /** @test */
    public function it_can_get_a_list_of_all_hotels(): void
    {
        $hotels = Hotel::factory()->count(3)->create();

        $this->hotelServiceMock
            ->shouldReceive('getAll')
            ->once()
            ->andReturn($hotels);

        $response = $this->getJson('/api/hotels');

        $response->assertStatus(200)
            ->assertJsonStructure(['data' => ['*' => ['id', 'name', 'address']]]);
    }

    /** @test */
    public function it_returns_404_when_showing_a_non_existent_hotel(): void
    {
        $this->hotelServiceMock
            ->shouldReceive('getByIdWithCity')
            ->once()
            ->with(999)
            ->andReturn(null);

        $response = $this->getJson('/api/hotels/999');

        $response->assertStatus(404)
            ->assertJson(['message' => 'Hotel not found']);
    }

    /** @test */
    public function it_can_store_a_new_hotel(): void
    {
        $city = City::factory()->create();

        $hotelData = [
            'name' => $this->faker->company(),
            'address' => $this->faker->address(),
            'nit' => $this->faker->unique()->numerify('##########'),
            'email' => $this->faker->unique()->safeEmail(),
            'city_id' => $city->id,
            'rooms_total' => 30,
            'room_configurations' => [
                ['room_type' => 'ESTANDAR', 'accommodation' => 'SENCILLA', 'quantity' => 10],
                ['room_type' => 'SUITE', 'accommodation' => 'DOBLE', 'quantity' => 20],
            ],
        ];

        $createdHotel = Hotel::factory()->for($city)->make($hotelData);
        $createdHotel->id = 1;

        $this->hotelServiceMock
            ->shouldReceive('create')
            ->once()
            ->with(Mockery::on(function ($data) use ($hotelData) {
                return $data['name'] === $hotelData['name']
                    && $data['nit'] === $hotelData['nit']
                    && count($data['room_configurations']) === 2;
            }))
            ->andReturn($createdHotel);

        $response = $this->postJson('/api/hotels', $hotelData);

        $response->assertStatus(201)
            ->assertJsonStructure(['data' => ['id', 'name', 'address']]);
    }

    /** @test */
    public function it_returns_422_on_store_with_invalid_data(): void
    {
        // Datos inválidos para disparar la validación
        $invalidData = [
            'name' => '', // requerido
            'address' => '123 Main St',
            'nit' => '123456789', // formato inválido
            'rooms_total' => 'cinco', // debe ser número
            'city_id' => 9999, // ciudad inexistente
            'room_configurations' => [], // no cumple reglas de validación
        ];

        $this->hotelServiceMock->shouldNotReceive('create');

        $response = $this->postJson('/api/hotels', $invalidData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors([
                'name',
                'nit',
                'rooms_total',
                'city_id',
                'room_configurations',
            ]);
    }

    /** @test */
    public function it_can_delete_an_existing_hotel(): void
    {
        $hotel = Hotel::factory()->create();

        $this->hotelServiceMock
            ->shouldReceive('delete')
            ->once()
            ->with($hotel->id)
            ->andReturn(true);

        $response = $this->deleteJson("/api/hotels/{$hotel->id}");

        $response->assertStatus(204)
            ->assertNoContent();
    }

    /** @test */
    public function it_returns_404_on_delete_of_non_existent_hotel(): void
    {
        $this->hotelServiceMock
            ->shouldReceive('delete')
            ->once()
            ->with(999)
            ->andReturn(false);

        $response = $this->deleteJson('/api/hotels/999');

        $response->assertStatus(404)
            ->assertJson(['message' => 'Hotel not found or could not be deleted']);
    }
}
