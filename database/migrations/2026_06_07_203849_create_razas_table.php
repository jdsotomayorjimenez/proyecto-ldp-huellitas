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
        Schema::create('razas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tipo_mascota_id')->constrained('tipos_mascotas');
            $table->string('nombre', 80);
            $table->string('descripcion', 255)->nullable();
            $table->timestamps();

            $table->unique(['tipo_mascota_id', 'nombre']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('razas');
    }
};
