<?php

namespace Database\Seeders;

use App\Models\RequisitoAdopcion;
use App\Models\TipoMascota;
use Illuminate\Database\Seeder;

class RequisitoAdopcionSeeder extends Seeder
{
    public function run(): void
    {
        $requisitosGenerales = [
            'Presentar cédula',
            'Firmar acta de compromiso',
            'Comprobante de domicilio',
        ];

        foreach (TipoMascota::all() as $tipo) {
            $requisitos = $requisitosGenerales;

            if ($tipo->nombre === 'Perro') {
                $requisitos[] = 'Llevar correa';
            } else {
                $requisitos[] = 'Llevar transportadora';
            }

            foreach ($requisitos as $nombre) {
                RequisitoAdopcion::updateOrCreate(
                    [
                        'tipo_mascota_id' => $tipo->id,
                        'nombre' => $nombre,
                    ],
                    [
                        'descripcion' => "Requisito para adoptar {$tipo->nombre}.",
                        'obligatorio' => true,
                        'estado' => 'activo',
                    ],
                );
            }
        }
    }
}
