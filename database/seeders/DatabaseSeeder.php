<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            UserSeeder::class,
            TipoMascotaSeeder::class,
            RazaSeeder::class,
            RequisitoAdopcionSeeder::class,
            CatalogoDemoSeeder::class,
            RazaDescripcionSeeder::class,
            AdopcionDemoSeeder::class,
        ]);
    }
}
