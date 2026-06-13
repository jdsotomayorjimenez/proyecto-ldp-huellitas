<?php

namespace Database\Seeders;

use App\Models\ImagenMascota;
use App\Models\Mascota;
use Illuminate\Database\Seeder;

class ImagenMascotaSeeder extends Seeder
{
    public function run(): void
    {
        foreach (['Luna', 'Max', 'Milo', 'Nala', 'Rocky'] as $nombre) {
            $mascota = Mascota::where('nombre', $nombre)->firstOrFail();

            ImagenMascota::updateOrCreate(
                [
                    'mascota_id' => $mascota->id,
                    'ruta' => 'img/mascotas/'.strtolower($nombre).'.svg',
                ],
                ['es_principal' => true],
            );
        }
    }
}
