<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
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
        'nit',
        'rooms_total',
        'city_id', // Corregido: Referencia a la clave foránea
        'user_id',

    ];

    /**
     * The attributes that should be cast.
     * Esto asegura que las fechas sean objetos Carbon automáticamente.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
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

    /**
     * Define la relación con el modelo User (pertenece a).
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
