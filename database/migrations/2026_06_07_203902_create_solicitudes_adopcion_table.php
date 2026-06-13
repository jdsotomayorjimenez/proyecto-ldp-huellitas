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
        Schema::create('solicitudes_adopcion', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('mascota_id')->constrained('mascotas');
            $table->string('motivo', 255);
            $table->string('experiencia', 255)->nullable();
            $table->enum('tipo_vivienda', ['casa', 'departamento', 'finca', 'otro']);
            $table->boolean('vivienda_propia')->default(false);
            $table->boolean('tiene_mascotas')->default(false);
            $table->enum('estado', [
                'pendiente',
                'aprobada',
                'rechazada',
                'cancelada',
            ])->default('pendiente');
            $table->timestamp('fecha_solicitud')->useCurrent();
            $table->timestamps();

            $table->unique(['user_id', 'mascota_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('solicitudes_adopcion');
    }
};
