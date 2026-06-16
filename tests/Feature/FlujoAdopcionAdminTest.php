<?php

namespace Tests\Feature;

use App\Models\Mascota;
use App\Models\Raza;
use App\Models\RequisitoAdopcion;
use App\Models\Role;
use App\Models\SolicitudAdopcion;
use App\Models\TipoMascota;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FlujoAdopcionAdminTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $rolAdmin = Role::create(['nombre' => 'Administrador']);
        Role::create(['nombre' => 'Adoptante']);
        $this->admin = User::factory()->create(['role_id' => $rolAdmin->id]);
    }

    /**
     * @return array{mascota: Mascota, solicitud: SolicitudAdopcion}
     */
    private function escenarioPendiente(): array
    {
        $tipo = TipoMascota::create(['nombre' => 'Perro']);
        $raza = Raza::create(['tipo_mascota_id' => $tipo->id, 'nombre' => 'Mestizo']);
        $mascota = Mascota::create([
            'raza_id' => $raza->id,
            'nombre' => 'Luna',
            'genero' => 'hembra',
            'tamanio' => 'mediano',
            'estado' => 'en_proceso',
        ]);
        $requisito = RequisitoAdopcion::create([
            'tipo_mascota_id' => $tipo->id,
            'nombre' => 'Presentar cédula',
            'obligatorio' => true,
            'estado' => 'activo',
        ]);

        $adoptante = User::factory()->create([
            'role_id' => Role::where('nombre', 'Adoptante')->first()->id,
        ]);

        $solicitud = SolicitudAdopcion::create([
            'user_id' => $adoptante->id,
            'mascota_id' => $mascota->id,
            'motivo' => 'Quiero adoptarla',
            'tipo_vivienda' => 'casa',
            'estado' => 'pendiente',
            'fecha_solicitud' => now(),
        ]);

        return compact('mascota', 'solicitud', 'requisito');
    }

    public function test_aprobar_agenda_cita_y_deja_requisitos_pendientes(): void
    {
        ['mascota' => $mascota, 'solicitud' => $solicitud, 'requisito' => $requisito] = $this->escenarioPendiente();

        $this->actingAs($this->admin)
            ->post(route('admin.solicitudes.responder', $solicitud), [
                'resultado' => 'aprobada',
                'respuesta' => '¡Felicidades! Te esperamos.',
                'fecha' => now()->addDays(3)->format('Y-m-d'),
                'hora' => '10:30',
                'lugar' => 'Refugio Huellitas',
            ])
            ->assertRedirect(route('admin.solicitudes.show', $solicitud));

        $this->assertDatabaseHas('respuestas_solicitud', [
            'solicitud_adopcion_id' => $solicitud->id,
            'resultado' => 'aprobada',
        ]);
        // Al aprobar se agenda la cita obligatoriamente.
        $this->assertDatabaseHas('citas_adopcion', [
            'solicitud_adopcion_id' => $solicitud->id,
            'estado' => 'programada',
        ]);
        // Los requisitos quedan pendientes para verificarse en la cita.
        $this->assertDatabaseHas('cumplimientos_requisitos', [
            'solicitud_adopcion_id' => $solicitud->id,
            'requisito_adopcion_id' => $requisito->id,
            'estado' => 'pendiente',
        ]);
        $this->assertDatabaseHas('solicitudes_adopcion', ['id' => $solicitud->id, 'estado' => 'aprobada']);
        $this->assertDatabaseHas('mascotas', ['id' => $mascota->id, 'estado' => 'en_proceso']);
    }

    public function test_no_se_completa_cita_sin_verificar_requisitos_obligatorios(): void
    {
        ['solicitud' => $solicitud] = $this->escenarioPendiente();

        $this->actingAs($this->admin)->post(route('admin.solicitudes.responder', $solicitud), [
            'resultado' => 'aprobada',
            'respuesta' => 'Aprobada para cita',
            'fecha' => now()->addDays(3)->format('Y-m-d'),
            'hora' => '10:30',
            'lugar' => 'Refugio Huellitas',
        ]);

        $cita = $solicitud->refresh()->cita;

        $this->actingAs($this->admin)
            ->patch(route('admin.citas.update', $cita), ['estado' => 'completada'])
            ->assertSessionHas('error');

        $this->assertDatabaseHas('citas_adopcion', ['id' => $cita->id, 'estado' => 'programada']);
    }

    public function test_rechazar_adopcion_en_cita_libera_mascota(): void
    {
        ['mascota' => $mascota, 'solicitud' => $solicitud, 'requisito' => $requisito] = $this->escenarioPendiente();

        $this->actingAs($this->admin)->post(route('admin.solicitudes.responder', $solicitud), [
            'resultado' => 'aprobada',
            'respuesta' => 'Aprobada',
            'fecha' => now()->addDays(2)->format('Y-m-d'),
            'hora' => '09:00',
            'lugar' => 'Refugio Huellitas',
        ]);

        $cita = $solicitud->refresh()->cita;

        // En la cita se rechaza la adopción (no se presentó / no cumplió).
        $this->actingAs($this->admin)
            ->patch(route('admin.citas.update', $cita), ['estado' => 'cancelada']);

        $this->assertDatabaseHas('citas_adopcion', ['id' => $cita->id, 'estado' => 'cancelada']);
        $this->assertDatabaseHas('solicitudes_adopcion', ['id' => $solicitud->id, 'estado' => 'cancelada']);
        $this->assertDatabaseHas('mascotas', ['id' => $mascota->id, 'estado' => 'disponible']);
    }

    public function test_rechazar_libera_la_mascota(): void
    {
        ['mascota' => $mascota, 'solicitud' => $solicitud] = $this->escenarioPendiente();

        $this->actingAs($this->admin)
            ->post(route('admin.solicitudes.responder', $solicitud), [
                'resultado' => 'rechazada',
                'respuesta' => 'No cumple los requisitos en este momento.',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('solicitudes_adopcion', ['id' => $solicitud->id, 'estado' => 'rechazada']);
        $this->assertDatabaseHas('mascotas', ['id' => $mascota->id, 'estado' => 'disponible']);
        $this->assertDatabaseHas('respuestas_solicitud', [
            'solicitud_adopcion_id' => $solicitud->id,
            'resultado' => 'rechazada',
        ]);
    }

    public function test_aprobar_requiere_datos_de_cita(): void
    {
        ['solicitud' => $solicitud] = $this->escenarioPendiente();

        // Aprobar sin fecha/hora/lugar de la cita debe fallar la validación.
        $this->actingAs($this->admin)
            ->post(route('admin.solicitudes.responder', $solicitud), [
                'resultado' => 'aprobada',
                'respuesta' => 'Aprobada pero sin cita',
            ])
            ->assertSessionHasErrors(['fecha', 'hora', 'lugar']);

        $this->assertDatabaseMissing('respuestas_solicitud', ['solicitud_adopcion_id' => $solicitud->id]);
        $this->assertDatabaseMissing('citas_adopcion', ['solicitud_adopcion_id' => $solicitud->id]);
    }

    public function test_registrar_adopcion_completa_el_flujo(): void
    {
        ['mascota' => $mascota, 'solicitud' => $solicitud, 'requisito' => $requisito] = $this->escenarioPendiente();

        // Aprobar agenda la cita, pero no registra adopcion ni confirma requisitos.
        $this->actingAs($this->admin)->post(route('admin.solicitudes.responder', $solicitud), [
            'resultado' => 'aprobada',
            'respuesta' => 'Aprobada',
            'fecha' => now()->addDays(2)->format('Y-m-d'),
            'hora' => '09:00',
            'lugar' => 'Refugio Huellitas',
        ]);

        $cita = $solicitud->refresh()->cita;
        $cumplimiento = $solicitud->cumplimientosRequisitos()->firstOrFail();

        $this->actingAs($this->admin)->patch(route('admin.cumplimientos.update', $cumplimiento), [
            'estado' => 'cumplido',
            'observacion' => 'Cedula revisada en la cita.',
        ]);

        $this->actingAs($this->admin)->patch(route('admin.citas.update', $cita), [
            'estado' => 'completada',
        ]);

        $response = $this->actingAs($this->admin)
            ->post(route('admin.adopciones.store', $solicitud), [
                'fecha_adopcion' => now()->format('Y-m-d'),
                'numero_acta' => 'ACTA-001',
                'observaciones' => 'Todo en orden.',
            ]);

        $adopcion = \App\Models\Adopcion::where('solicitud_adopcion_id', $solicitud->id)->firstOrFail();
        $response->assertRedirect(route('admin.adopciones.show', $adopcion));

        $this->assertDatabaseHas('adopciones', [
            'solicitud_adopcion_id' => $solicitud->id,
            'numero_acta' => 'ACTA-001',
        ]);
        $this->assertDatabaseHas('mascotas', ['id' => $mascota->id, 'estado' => 'adoptada']);
        $this->assertDatabaseHas('citas_adopcion', [
            'solicitud_adopcion_id' => $solicitud->id,
            'estado' => 'completada',
        ]);
    }

    public function test_adoptante_no_puede_ver_solicitudes_admin(): void
    {
        $adoptante = User::factory()->create([
            'role_id' => Role::where('nombre', 'Adoptante')->first()->id,
        ]);

        $this->actingAs($adoptante)
            ->get(route('admin.solicitudes.index'))
            ->assertForbidden();
    }
}
