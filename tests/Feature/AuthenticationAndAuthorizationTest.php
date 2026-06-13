<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthenticationAndAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registro_crea_un_adoptante_con_password_cifrado(): void
    {
        $rol = Role::create([
            'nombre' => 'Adoptante',
            'descripcion' => 'Usuario adoptante',
        ]);

        $response = $this->post(route('register.store'), [
            'name' => 'Usuario Nuevo',
            'email' => 'nuevo@example.com',
            'cedula' => '0912345678',
            'fecha_nacimiento' => '1995-01-01',
            'telefono' => '0991234567',
            'direccion' => 'Guayaquil',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertRedirect(route('home'));
        $usuario = User::where('email', 'nuevo@example.com')->firstOrFail();

        $this->assertAuthenticatedAs($usuario);
        $this->assertSame($rol->id, $usuario->role_id);
        $this->assertTrue(Hash::check('password', $usuario->password));
        $this->assertNotSame('password', $usuario->password);
    }

    public function test_invitado_no_puede_acceder_al_panel_admin(): void
    {
        $this->get(route('admin.dashboard'))->assertRedirect(route('login'));
    }

    public function test_adoptante_recibe_403_en_el_panel_admin(): void
    {
        $adoptante = $this->crearUsuarioConRol('Adoptante');

        $this->actingAs($adoptante)
            ->get(route('admin.dashboard'))
            ->assertForbidden();
    }

    public function test_administrador_puede_acceder_al_panel_admin(): void
    {
        $administrador = $this->crearUsuarioConRol('Administrador');

        $this->actingAs($administrador)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('Dashboard');
    }

    public function test_administrador_puede_registrar_adoptante_sin_cambiar_su_sesion(): void
    {
        $administrador = $this->crearUsuarioConRol('Administrador');
        Role::create([
            'nombre' => 'Adoptante',
            'descripcion' => 'Usuario adoptante',
        ]);

        $this->actingAs($administrador)
            ->post(route('admin.adoptantes.store'), [
                'name' => 'Adoptante Nuevo',
                'email' => 'adoptante.nuevo@example.com',
                'cedula' => '0911111111',
                'fecha_nacimiento' => '1998-05-10',
                'telefono' => '0991111111',
                'direccion' => 'Guayaquil',
                'password' => 'password',
                'password_confirmation' => 'password',
            ])
            ->assertRedirect(route('admin.dashboard'));

        $this->assertAuthenticatedAs($administrador);
        $this->assertDatabaseHas('users', [
            'email' => 'adoptante.nuevo@example.com',
        ]);
    }

    public function test_ruta_registro_redirige_al_formulario_admin_si_hay_sesion_admin(): void
    {
        $administrador = $this->crearUsuarioConRol('Administrador');

        $this->actingAs($administrador)
            ->get(route('register'))
            ->assertRedirect(route('admin.adoptantes.create'));
    }

    private function crearUsuarioConRol(string $nombreRol): User
    {
        $rol = Role::create([
            'nombre' => $nombreRol,
            'descripcion' => "Rol {$nombreRol}",
        ]);

        return User::factory()->create(['role_id' => $rol->id]);
    }
}
