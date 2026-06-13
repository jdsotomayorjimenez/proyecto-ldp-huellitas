<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $administrador = Role::where('nombre', 'Administrador')->firstOrFail();
        $adoptante = Role::where('nombre', 'Adoptante')->firstOrFail();

        User::updateOrCreate(
            ['email' => 'admin@huellitas.com'],
            [
                'role_id' => $administrador->id,
                'name' => 'Administrador Huellitas',
                'cedula' => '0999999999',
                'fecha_nacimiento' => '1990-01-01',
                'password' => 'password',
                'telefono' => '0999999999',
                'direccion' => 'Refugio Huellitas',
                'email_verified_at' => now(),
            ],
        );

        User::updateOrCreate(
            ['email' => 'adoptante@huellitas.com'],
            [
                'role_id' => $adoptante->id,
                'name' => 'Adoptante de Prueba',
                'cedula' => '0888888888',
                'fecha_nacimiento' => '1995-05-15',
                'password' => 'password',
                'telefono' => '0988888888',
                'direccion' => 'Guayaquil',
                'email_verified_at' => now(),
            ],
        );
    }
}
