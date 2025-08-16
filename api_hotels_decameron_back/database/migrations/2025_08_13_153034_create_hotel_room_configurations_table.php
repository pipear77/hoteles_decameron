<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('hotel_room_configurations', function (Blueprint $table) {
            $table->id();

            $table->foreignId('hotel_id')
                ->constrained('hotels')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->foreignId('room_type_id')
                ->constrained('room_types')
                ->onUpdate('cascade')
                ->onDelete('restrict');

            $table->foreignId('accommodation_id')
                ->constrained('accommodations')
                ->onUpdate('cascade')
                ->onDelete('restrict');

            $table->unsignedSmallInteger('quantity');
            $table->timestamps();

            // Restricción única con un nombre más claro
            $table->unique(['hotel_id', 'room_type_id', 'accommodation_id'], 'hotel_room_config_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hotel_rooms');
    }
};
