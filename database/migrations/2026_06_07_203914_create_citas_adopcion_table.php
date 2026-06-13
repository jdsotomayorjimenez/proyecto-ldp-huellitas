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
        Schema::create('citas_adopcion', function (Blueprint $table) {
            $table->id();
            $table->foreignId('solicitud_adopcion_id')
                ->unique()
                ->constrained('solicitudes_adopcion');
            $table->date('fecha');
            $table->time('hora');
            $table->string('lugar', 150);
            $table->string('indicaciones', 255)->nullable();
            $table->enum('estado', [
                'programada',
                'completada',
                'cancelada',
            ])->default('programada');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('citas_adopcion');
    }
};
