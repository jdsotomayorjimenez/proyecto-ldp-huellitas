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
            'Perro' => [
                'Mestizo',
                'Labrador',
                'Poodle',
                'Pomerania',
                'Yorkshire Terrier',
                'Golden Retriever',
                'Bulldog Francés',
                'Corgi',
                'Husky',
                'Chihuahua',
                'Schnauzer',
                'Pastor Alemán',
            ],
            'Gato' => ['Mestizo', 'Siamés', 'Persa', 'Maine Coon'],
            'Conejo' => ['Mestizo', 'Holland Lop', 'Cabeza de León'],
            'Ave' => ['Mestizo', 'Periquito Australiano', 'Canario', 'Cacatúa Ninfa'],
            'Hámster' => ['Mestizo', 'Sirio', 'Roborovski'],
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
