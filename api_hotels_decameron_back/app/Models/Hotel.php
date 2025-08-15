<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Hotel extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'address',
        'city',
        'country',
        'nit',
        'rooms_total',
    ];

    /**
     * Get the room configurations for the hotel.
     *
     * @return HasMany
     */
    public function hotelRooms(): HasMany
    {
        return $this->hasMany(HotelRoomConfiguration::class);
    }
}
