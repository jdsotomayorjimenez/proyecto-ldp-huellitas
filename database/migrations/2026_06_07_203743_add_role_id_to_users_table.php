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
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('role_id')->after('id')->constrained('roles');
            $table->string('cedula', 10)->nullable()->unique()->after('email');
            $table->date('fecha_nacimiento')->nullable()->after('cedula');
            $table->string('telefono', 10)->nullable()->after('password');
            $table->string('direccion', 255)->nullable()->after('telefono');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['role_id']);
            $table->dropColumn([
                'role_id',
                'cedula',
                'fecha_nacimiento',
                'telefono',
                'direccion',
            ]);
        });
    }
};
