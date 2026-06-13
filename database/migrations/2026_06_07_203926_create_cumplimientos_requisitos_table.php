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
        Schema::create('cumplimientos_requisitos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('solicitud_adopcion_id')
                ->constrained('solicitudes_adopcion');
            $table->foreignId('requisito_adopcion_id')
                ->constrained('requisitos_adopcion');
            $table->enum('estado', [
                'pendiente',
                'cumplido',
                'no_cumplido',
                'no_aplica',
            ])->default('pendiente');
            $table->string('observacion', 255)->nullable();
            $table->date('fecha_revision')->nullable();
            $table->timestamps();

            $table->unique([
                'solicitud_adopcion_id',
                'requisito_adopcion_id',
            ], 'cumplimientos_solicitud_requisito_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cumplimientos_requisitos');
    }
};
