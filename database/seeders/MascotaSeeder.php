<?php

namespace Database\Seeders;

use App\Models\Mascota;
use App\Models\Raza;
use Illuminate\Database\Seeder;

class MascotaSeeder extends Seeder
{
    public function run(): void
    {
        $mascotas = [
            [
                'nombre' => 'Luna',
                'tipo' => 'Perro',
                'raza' => 'Mestizo',
                'fecha_nacimiento' => '2022-03-10',
                'genero' => 'hembra',
                'tamanio' => 'mediano',
                'descripcion' => 'Cariñosa, tranquila y sociable.',
            ],
            [
                'nombre' => 'Max',
                'tipo' => 'Perro',
                'raza' => 'Labrador',
                'fecha_nacimiento' => '2021-08-20',
                'genero' => 'macho',
                'tamanio' => 'grande',
                'descripcion' => 'Juguetón y con mucha energía.',
            ],
            [
                'nombre' => 'Milo',
                'tipo' => 'Gato',
                'raza' => 'Siamés',
                'fecha_nacimiento' => '2023-01-12',
                'genero' => 'macho',
                'tamanio' => 'pequeno',
                'descripcion' => 'Curioso y muy apegado a las personas.',
            ],
            [
                'nombre' => 'Nala',
                'tipo' => 'Gato',
                'raza' => 'Persa',
                'fecha_nacimiento' => '2022-11-05',
                'genero' => 'hembra',
                'tamanio' => 'pequeno',
                'descripcion' => 'Tranquila y acostumbrada a vivir dentro de casa.',
            ],
            [
                'nombre' => 'Rocky',
                'tipo' => 'Perro',
                'raza' => 'Poodle',
                'fecha_nacimiento' => '2020-06-18',
                'genero' => 'macho',
                'tamanio' => 'mediano',
                'descripcion' => 'Obediente y amigable con otras mascotas.',
            ],
        ];

        foreach ($mascotas as $datos) {
            $raza = Raza::where('nombre', $datos['raza'])
                ->whereHas('tipoMascota', fn ($query) => $query->where('nombre', $datos['tipo']))
                ->firstOrFail();

            unset($datos['tipo'], $datos['raza']);

            Mascota::updateOrCreate(
                ['nombre' => $datos['nombre']],
                [
                    ...$datos,
                    'raza_id' => $raza->id,
                    'estado' => 'disponible',
                ],
            );
        }
    }
}
