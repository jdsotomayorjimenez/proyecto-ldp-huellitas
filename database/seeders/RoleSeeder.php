<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        Role::updateOrCreate(
            ['nombre' => 'Administrador'],
            ['descripcion' => 'Gestiona la información y el panel administrativo'],
        );

        Role::updateOrCreate(
            ['nombre' => 'Adoptante'],
            ['descripcion' => 'Usuario que solicita adoptar mascotas'],
        );
    }
}
