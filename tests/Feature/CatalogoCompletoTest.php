<?php

namespace Tests\Feature;

use App\Models\ImagenMascota;
use App\Models\Mascota;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class CatalogoCompletoTest extends TestCase
{
    use RefreshDatabase;

    public function test_seeder_registra_las_79_mascotas_con_la_distribucion_solicitada(): void
    {
        $this->seed();

        $this->assertSame(79, Mascota::count());
        $this->assertSame(79, ImagenMascota::count());

        $esperados = [
            'Perro' => 30,
            'Gato' => 20,
            'Ave' => 15,
            'Conejo' => 7,
            'Hámster' => 7,
        ];

        foreach ($esperados as $tipo => $cantidad) {
            $this->assertSame(
                $cantidad,
                Mascota::whereHas(
                    'raza.tipoMascota',
                    fn ($query) => $query->where('nombre', $tipo),
                )->count(),
                "El tipo {$tipo} no tiene {$cantidad} registros.",
            );
        }
    }

    public function test_todas_las_mascotas_tienen_datos_e_imagen_real_local(): void
    {
        $this->seed();

        Mascota::with(['raza.tipoMascota', 'imagenes'])->get()->each(function (Mascota $mascota): void {
            $this->assertNotEmpty($mascota->nombre);
            $this->assertNotNull($mascota->fecha_nacimiento);
            $this->assertContains($mascota->genero, ['macho', 'hembra']);
            $this->assertNotEmpty($mascota->descripcion);
            $this->assertNotEmpty($mascota->raza->nombre);
            $this->assertCount(1, $mascota->imagenes);
            $this->assertFileExists(public_path($mascota->imagenes->first()->ruta));
        });
    }

    public function test_edad_legible_muestra_meses_y_anios(): void
    {
        Carbon::setTestNow('2026-06-14');

        $cachorro = new Mascota(['fecha_nacimiento' => '2026-04-14']);
        $mayor = new Mascota(['fecha_nacimiento' => '2016-03-14']);

        $this->assertSame('2 meses', $cachorro->edad_legible);
        $this->assertSame('10 años y 3 meses', $mayor->edad_legible);

        Carbon::setTestNow();
    }
}
