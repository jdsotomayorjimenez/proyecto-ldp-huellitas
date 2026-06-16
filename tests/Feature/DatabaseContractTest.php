<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class DatabaseContractTest extends TestCase
{
    use RefreshDatabase;

    public function test_modelo_relacional_contiene_las_doce_tablas_requeridas(): void
    {
        $tablas = [
            'roles',
            'users',
            'tipos_mascotas',
            'razas',
            'mascotas',
            'imagenes_mascotas',
            'solicitudes_adopcion',
            'respuestas_solicitud',
            'citas_adopcion',
            'requisitos_adopcion',
            'cumplimientos_requisitos',
            'adopciones',
        ];

        foreach ($tablas as $tabla) {
            $this->assertTrue(Schema::hasTable($tabla), "Falta la tabla {$tabla}.");
        }

        $this->assertFalse(Schema::hasTable('administradores'));
    }

    public function test_columnas_criticas_respetan_el_contrato(): void
    {
        $this->assertTrue(Schema::hasColumns('users', [
            'role_id',
            'password',
            'cedula',
            'fecha_nacimiento',
            'telefono',
            'direccion',
        ]));

        $this->assertTrue(Schema::hasColumns('mascotas', [
            'raza_id',
            'nombre',
            'genero',
            'tamanio',
            'estado',
        ]));

        $this->assertFalse(Schema::hasColumn('mascotas', 'registrado_por'));
        $this->assertFalse(Schema::hasColumn('users', 'password_hash'));
        $this->assertTrue(Schema::hasColumn('requisitos_adopcion', 'tipo_mascota_id'));
    }

    public function test_seeders_crean_los_datos_minimos_de_la_parte_uno(): void
    {
        $this->seed();

        foreach (['Administrador', 'Adoptante'] as $rol) {
            $this->assertDatabaseHas('roles', ['nombre' => $rol]);
        }

        foreach (['Perro', 'Gato', 'Conejo', 'Ave', 'Hámster'] as $tipo) {
            $this->assertDatabaseHas('tipos_mascotas', ['nombre' => $tipo]);
        }

        foreach (['Luna', 'Max', 'Milo', 'Nala', 'Rocky'] as $mascota) {
            $this->assertDatabaseHas('mascotas', ['nombre' => $mascota]);
        }

        $this->assertDatabaseHas('users', ['email' => 'admin@huellitas.com']);
        $this->assertDatabaseHas('users', ['email' => 'adoptante@huellitas.com']);
        $this->assertGreaterThanOrEqual(5, \App\Models\ImagenMascota::count());
        $this->assertGreaterThan(0, \App\Models\RequisitoAdopcion::count());
    }
}
