<?php

namespace Tests\Feature;

use App\Models\Mascota;
use App\Models\Raza;
use App\Models\Role;
use App\Models\TipoMascota;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicCatalogoTest extends TestCase
{
    use RefreshDatabase;

    private function crearMascota(array $attrs = []): Mascota
    {
        $tipo = TipoMascota::firstOrCreate(['nombre' => $attrs['tipo'] ?? 'Perro']);
        $raza = Raza::firstOrCreate([
            'tipo_mascota_id' => $tipo->id,
            'nombre' => $attrs['raza'] ?? 'Mestizo',
        ]);

        return Mascota::create(array_merge([
            'raza_id' => $raza->id,
            'nombre' => 'Luna',
            'genero' => 'hembra',
            'tamanio' => 'mediano',
            'estado' => 'disponible',
        ], array_diff_key($attrs, array_flip(['tipo', 'raza']))));
    }

    private function adoptante(): User
    {
        $rol = Role::firstOrCreate(['nombre' => 'Adoptante']);

        return User::factory()->create(['role_id' => $rol->id]);
    }

    public function test_home_publica_lista_mascotas_disponibles(): void
    {
        $this->crearMascota(['nombre' => 'Firulais']);

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('Firulais');
    }

    public function test_catalogo_filtra_por_genero(): void
    {
        $this->crearMascota(['nombre' => 'Luna', 'genero' => 'hembra']);
        $this->crearMascota(['nombre' => 'Max', 'genero' => 'macho', 'raza' => 'Labrador']);

        $this->get(route('mascotas.catalogo', ['genero' => 'macho']))
            ->assertOk()
            ->assertSee('Max')
            ->assertDontSee('>Luna<', false);
    }

    public function test_detalle_muestra_la_mascota(): void
    {
        $mascota = $this->crearMascota(['nombre' => 'Nala']);

        $this->get(route('mascotas.show.public', $mascota))
            ->assertOk()
            ->assertSee('Nala');
    }

    public function test_invitado_es_redirigido_a_login_al_solicitar(): void
    {
        $mascota = $this->crearMascota();

        $this->get(route('solicitudes.create', $mascota))
            ->assertRedirect(route('login'));
    }

    public function test_adoptante_envia_solicitud_y_mascota_pasa_a_en_proceso(): void
    {
        $mascota = $this->crearMascota(['nombre' => 'Luna']);
        $adoptante = $this->adoptante();

        $this->actingAs($adoptante)
            ->post(route('solicitudes.store', $mascota), [
                'motivo' => 'Quiero darle un hogar lleno de amor.',
                'experiencia' => 'He tenido perros antes.',
                'tipo_vivienda' => 'casa',
                'vivienda_propia' => '1',
            ])
            ->assertRedirect(route('solicitudes.mis'));

        $this->assertDatabaseHas('solicitudes_adopcion', [
            'mascota_id' => $mascota->id,
            'user_id' => $adoptante->id,
            'estado' => 'pendiente',
        ]);

        $this->assertDatabaseHas('mascotas', [
            'id' => $mascota->id,
            'estado' => 'en_proceso',
        ]);
    }

    public function test_no_se_puede_solicitar_una_mascota_no_disponible(): void
    {
        $mascota = $this->crearMascota(['estado' => 'adoptada']);
        $adoptante = $this->adoptante();

        $this->actingAs($adoptante)
            ->post(route('solicitudes.store', $mascota), [
                'motivo' => 'Quiero adoptar.',
                'tipo_vivienda' => 'casa',
            ])
            ->assertSessionHas('error');

        $this->assertDatabaseMissing('solicitudes_adopcion', ['mascota_id' => $mascota->id]);
    }

    public function test_no_se_puede_solicitar_la_misma_mascota_dos_veces(): void
    {
        $mascota = $this->crearMascota();
        $adoptante = $this->adoptante();

        $this->actingAs($adoptante)->post(route('solicitudes.store', $mascota), [
            'motivo' => 'Primera solicitud.',
            'tipo_vivienda' => 'casa',
        ]);

        // Simulamos que la mascota volvió a estar disponible (p. ej. tras un rechazo).
        Mascota::where('id', $mascota->id)->update(['estado' => 'disponible']);

        $this->actingAs($adoptante)
            ->get(route('solicitudes.create', $mascota))
            ->assertRedirect(route('solicitudes.mis'))
            ->assertSessionHas('error');

        $this->assertSame(1, $mascota->solicitudesAdopcion()->count());
    }

    public function test_administrador_no_puede_enviar_solicitudes(): void
    {
        $mascota = $this->crearMascota();
        $rol = Role::firstOrCreate(['nombre' => 'Administrador']);
        $admin = User::factory()->create(['role_id' => $rol->id]);

        $this->actingAs($admin)
            ->post(route('solicitudes.store', $mascota), [
                'motivo' => 'No debería poder.',
                'tipo_vivienda' => 'casa',
            ])
            ->assertForbidden();
    }
}
