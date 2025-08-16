<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class City extends Model
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
        'nit',
        'rooms_total',
        'city_id', // Corregido: Referencia a la clave foránea
    ];

    /**
     * Get the city that this hotel belongs to.
     *
     * @return BelongsTo
     */
    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    /**
     * Get the room configurations for the hotel.
     *
     * @return HasMany
     */
    public function roomConfigurations(): HasMany
    {
        return $this->hasMany(HotelRoomConfiguration::class);
    }
}
