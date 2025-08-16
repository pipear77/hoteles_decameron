<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Corre la migración.
     */
    public function up(): void
    {
        Schema::create('hotels', function (Blueprint $table) {
            $table->id();
            $table->string('name', 150);
            $table->string('address', 255);
            $table->string('nit', 50)->unique();
            $table->unsignedSmallInteger('rooms_total');

            // Agregamos la clave foránea para el usuario
            $table->foreignId('user_id')
                ->constrained()
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->foreignId('city_id')
                ->constrained('cities')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->timestamps();

            // Restricción única para nombre de hotel y ciudad.
            // Esto evita que un mismo usuario tenga dos hoteles con el mismo nombre en la misma ciudad.
            $table->unique(['name', 'city_id', 'user_id']);

            // Agregamos un índice a la columna user_id para búsquedas más rápidas.
            $table->index('user_id');
            $table->index('city_id');
        });
    }

    /**
     * Revierte la migración.
     */
    public function down(): void
    {
        Schema::dropIfExists('hotels');
    }
};
