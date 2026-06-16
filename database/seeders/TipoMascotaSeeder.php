<?php

namespace Database\Seeders;

use App\Models\TipoMascota;
use Illuminate\Database\Seeder;

class TipoMascotaSeeder extends Seeder
{
    public function run(): void
    {
        $tipos = [
            'Perro' => 'Perros disponibles para adopción.',
            'Gato' => 'Gatos disponibles para adopción.',
            'Conejo' => 'Conejos disponibles para adopción.',
            'Ave' => 'Aves disponibles para adopción.',
            'Hámster' => 'Hámsters disponibles para adopción.',
        ];

        foreach ($tipos as $nombre => $descripcion) {
            TipoMascota::updateOrCreate(
                ['nombre' => $nombre],
                ['descripcion' => $descripcion],
            );
        }
    }
}
