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
        Schema::create('respuestas_solicitud', function (Blueprint $table) {
            $table->id();
            $table->foreignId('solicitud_adopcion_id')
                ->unique()
                ->constrained('solicitudes_adopcion');
            $table->foreignId('administrador_id')->constrained('users');
            $table->enum('resultado', ['aprobada', 'rechazada']);
            $table->string('respuesta', 255);
            $table->date('fecha_respuesta');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('respuestas_solicitud');
    }
};
