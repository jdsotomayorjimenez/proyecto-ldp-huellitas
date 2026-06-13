<?php

namespace Tests\Feature;

use App\Models\Mascota;
use App\Models\Raza;
use App\Models\RequisitoAdopcion;
use App\Models\Role;
use App\Models\TipoMascota;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminCrudTest extends TestCase
{
    use RefreshDatabase;

    private User $administrador;

    protected function setUp(): void
    {
        parent::setUp();

        $rol = Role::create([
            'nombre' => 'Administrador',
            'descripcion' => 'Administrador del sistema',
        ]);

        $this->administrador = User::factory()->create(['role_id' => $rol->id]);
    }

    public function test_administrador_puede_crear_datos_base(): void
    {
        $this->actingAs($this->administrador)
            ->post(route('admin.tipos-mascotas.store'), [
                'nombre' => 'Perro',
                'descripcion' => 'Perros',
            ])
            ->assertRedirect(route('admin.tipos-mascotas.index'));

        $tipo = TipoMascota::where('nombre', 'Perro')->firstOrFail();

        $this->actingAs($this->administrador)
            ->post(route('admin.razas.store'), [
                'tipo_mascota_id' => $tipo->id,
                'nombre' => 'Mestizo',
                'descripcion' => 'Sin raza específica',
            ])
            ->assertRedirect(route('admin.razas.index'));

        $raza = Raza::where('nombre', 'Mestizo')->firstOrFail();

        $this->actingAs($this->administrador)
            ->post(route('admin.mascotas.store'), [
                'raza_id' => $raza->id,
                'nombre' => 'Luna',
                'fecha_nacimiento' => '2022-01-01',
                'genero' => 'hembra',
                'tamanio' => 'mediano',
                'descripcion' => 'Mascota de prueba',
                'estado' => 'disponible',
            ])
            ->assertRedirect();

        $this->actingAs($this->administrador)
            ->post(route('admin.requisitos.store'), [
                'alcance' => 'tipo',
                'tipo_mascota_id' => $tipo->id,
                'nombre' => 'Presentar cédula',
                'descripcion' => 'Documento de identidad',
                'obligatorio' => '1',
                'estado' => 'activo',
            ])
            ->assertRedirect(route('admin.requisitos.index'));

        $this->assertDatabaseHas('mascotas', ['nombre' => 'Luna']);
        $this->assertDatabaseHas('requisitos_adopcion', [
            'nombre' => 'Presentar cédula',
            'tipo_mascota_id' => $tipo->id,
        ]);
    }

    public function test_no_se_puede_eliminar_raza_con_mascotas(): void
    {
        $tipo = TipoMascota::create(['nombre' => 'Perro']);
        $raza = Raza::create([
            'tipo_mascota_id' => $tipo->id,
            'nombre' => 'Mestizo',
        ]);
        Mascota::create([
            'raza_id' => $raza->id,
            'nombre' => 'Luna',
            'genero' => 'hembra',
            'tamanio' => 'mediano',
            'estado' => 'disponible',
        ]);

        $this->actingAs($this->administrador)
            ->delete(route('admin.razas.destroy', $raza))
            ->assertSessionHas('error');

        $this->assertDatabaseHas('razas', ['id' => $raza->id]);
    }

    public function test_requisito_siempre_pertenece_a_un_tipo(): void
    {
        $this->actingAs($this->administrador)
            ->post(route('admin.requisitos.store'), [
                'alcance' => 'tipo',
                'nombre' => 'Presentar cédula',
                'obligatorio' => '1',
                'estado' => 'activo',
            ])
            ->assertSessionHasErrors('tipo_mascota_id');

        $this->assertSame(0, RequisitoAdopcion::count());
    }

    public function test_requisito_general_permite_excepciones_de_obligatoriedad(): void
    {
        $perro = TipoMascota::create(['nombre' => 'Perro']);
        $conejo = TipoMascota::create(['nombre' => 'Conejo']);

        $this->actingAs($this->administrador)
            ->post(route('admin.requisitos.store'), [
                'alcance' => 'general',
                'tipos' => [$perro->id, $conejo->id],
                'obligatorios' => [
                    $perro->id => '1',
                    $conejo->id => '0',
                ],
                'nombre' => 'Comprobante de domicilio',
                'descripcion' => 'Requisito general de prueba',
                'obligatorio' => '0',
                'estado' => 'activo',
            ])
            ->assertRedirect(route('admin.requisitos.index'));

        $this->assertDatabaseHas('requisitos_adopcion', [
            'tipo_mascota_id' => $perro->id,
            'nombre' => 'Comprobante de domicilio',
            'obligatorio' => true,
        ]);
        $this->assertDatabaseHas('requisitos_adopcion', [
            'tipo_mascota_id' => $conejo->id,
            'nombre' => 'Comprobante de domicilio',
            'obligatorio' => false,
        ]);
    }

    public function test_listado_de_requisitos_se_puede_filtrar_por_tipo(): void
    {
        $perro = TipoMascota::create(['nombre' => 'Perro']);
        $gato = TipoMascota::create(['nombre' => 'Gato']);
        RequisitoAdopcion::create([
            'tipo_mascota_id' => $perro->id,
            'nombre' => 'Correa',
            'obligatorio' => true,
            'estado' => 'activo',
        ]);
        RequisitoAdopcion::create([
            'tipo_mascota_id' => $gato->id,
            'nombre' => 'Transportadora',
            'obligatorio' => true,
            'estado' => 'activo',
        ]);

        $this->actingAs($this->administrador)
            ->get(route('admin.requisitos.index', ['tipo_mascota_id' => $perro->id]))
            ->assertOk()
            ->assertSee('Correa')
            ->assertDontSee('Transportadora');
    }

    public function test_formulario_de_mascota_incluye_busqueda_y_filtro_de_raza(): void
    {
        $tipo = TipoMascota::create(['nombre' => 'Hámsters']);
        Raza::create([
            'tipo_mascota_id' => $tipo->id,
            'nombre' => 'Dorado',
        ]);

        $this->actingAs($this->administrador)
            ->get(route('admin.mascotas.create'))
            ->assertOk()
            ->assertSee('Selecciona un tipo')
            ->assertSee('Selecciona primero un tipo')
            ->assertSee('Escribe para filtrar razas')
            ->assertSee('"nombre":"Dorado"', false);
    }
}
