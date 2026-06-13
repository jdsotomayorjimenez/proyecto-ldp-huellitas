<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ImagenMascota;
use App\Models\Mascota;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ImagenMascotaController extends Controller
{
    public function store(Request $request, Mascota $mascota): RedirectResponse
    {
        $datos = $request->validate([
            'ruta' => [
                'required',
                'string',
                'max:255',
                'regex:/^img\/mascotas\/[A-Za-z0-9._-]+$/',
                'unique:imagenes_mascotas,ruta,NULL,id,mascota_id,'.$mascota->id,
            ],
            'es_principal' => ['nullable', 'boolean'],
        ]);

        if (! is_file(public_path($datos['ruta']))) {
            throw ValidationException::withMessages([
                'ruta' => 'El archivo indicado no existe dentro de public/.',
            ]);
        }

        DB::transaction(function () use ($mascota, $datos): void {
            $esPrincipal = (bool) ($datos['es_principal'] ?? false);

            if ($esPrincipal || ! $mascota->imagenes()->exists()) {
                $mascota->imagenes()->update(['es_principal' => false]);
                $esPrincipal = true;
            }

            $mascota->imagenes()->create([
                'ruta' => $datos['ruta'],
                'es_principal' => $esPrincipal,
            ]);
        });

        return back()->with('success', 'Imagen asociada correctamente.');
    }

    public function destroy(ImagenMascota $imagenMascota): RedirectResponse
    {
        $mascota = $imagenMascota->mascota;
        $eraPrincipal = $imagenMascota->es_principal;
        $imagenMascota->delete();

        if ($eraPrincipal) {
            $mascota->imagenes()->oldest()->first()?->update(['es_principal' => true]);
        }

        return back()->with('success', 'Imagen eliminada correctamente.');
    }
}
