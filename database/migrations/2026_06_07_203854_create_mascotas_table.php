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
        Schema::create('mascotas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('raza_id')->constrained('razas');
            $table->string('nombre', 100);
            $table->date('fecha_nacimiento')->nullable();
            $table->enum('genero', ['macho', 'hembra']);
            $table->enum('tamanio', ['pequeno', 'mediano', 'grande']);
            $table->string('descripcion', 255)->nullable();
            $table->enum('estado', [
                'disponible',
                'en_proceso',
                'adoptada',
                'no_disponible',
            ])->default('disponible');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mascotas');
    }
};
