<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Accommodation extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'description',
    ];

    /**
     * Get the room configurations that use this accommodation.
     *
     * @return HasMany
     */
    public function roomConfigurations(): HasMany
    {
        return $this->hasMany(HotelRoomConfiguration::class);
    }
}
