<?php

namespace Database\Seeders;

use App\Models\Raza;
use App\Models\TipoMascota;
use Illuminate\Database\Seeder;

class RazaSeeder extends Seeder
{
    public function run(): void
    {
        $razas = [
            'Perro' => ['Mestizo', 'Labrador', 'Poodle'],
            'Gato' => ['Mestizo', 'Siamés', 'Persa'],
            'Conejo' => ['Sin raza definida'],
            'Ave' => ['Sin raza definida'],
        ];

        foreach ($razas as $tipoNombre => $nombres) {
            $tipo = TipoMascota::where('nombre', $tipoNombre)->firstOrFail();

            foreach ($nombres as $nombre) {
                Raza::updateOrCreate(
                    [
                        'tipo_mascota_id' => $tipo->id,
                        'nombre' => $nombre,
                    ],
                    ['descripcion' => "Raza {$nombre} para {$tipoNombre}."],
                );
            }
        }
    }
}
