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
        Schema::create('hotels', function (Blueprint $table) {
            $table->id();
            $table->string('name', 150);
            $table->string('address', 255);
            $table->string('city', 100);
            $table->string('country', 100)->default('Colombia');
            $table->string('nit', 50)->unique();
            $table->unsignedSmallInteger('rooms_total');
            $table->timestamps();

            $table->unique(['name', 'city']);
            $table->index(['city', 'country']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hotels');
    }
};
