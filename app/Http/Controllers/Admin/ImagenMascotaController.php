<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ImagenMascota;
use App\Models\Mascota;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class ImagenMascotaController extends Controller
{
    public function store(Request $request, Mascota $mascota): RedirectResponse
    {
        $datos = $request->validate([
            'ruta' => [
                'nullable',
                'required_without:foto',
                'string',
                'max:255',
                'regex:/^img\/mascotas\/(?:[A-Za-z0-9._-]+\/)*[A-Za-z0-9._-]+$/',
                'unique:imagenes_mascotas,ruta,NULL,id,mascota_id,'.$mascota->id,
            ],
            'foto' => [
                'nullable',
                'required_without:ruta',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:5120',
            ],
            'es_principal' => ['nullable', 'boolean'],
        ]);

        $ruta = $datos['ruta'] ?? null;

        if ($request->hasFile('foto')) {
            $directorio = public_path('img/mascotas/uploads');
            File::ensureDirectoryExists($directorio);

            $extension = $request->file('foto')->extension();
            $nombreArchivo = Str::slug($mascota->nombre).'-'.Str::uuid().'.'.$extension;
            $request->file('foto')->move($directorio, $nombreArchivo);
            $ruta = 'img/mascotas/uploads/'.$nombreArchivo;
        }

        if (! $ruta || ! is_file(public_path($ruta))) {
            throw ValidationException::withMessages([
                'ruta' => 'El archivo indicado no existe dentro de public/.',
            ]);
        }

        DB::transaction(function () use ($mascota, $datos, $ruta): void {
            $esPrincipal = (bool) ($datos['es_principal'] ?? false);

            if ($esPrincipal || ! $mascota->imagenes()->exists()) {
                $mascota->imagenes()->update(['es_principal' => false]);
                $esPrincipal = true;
            }

            $mascota->imagenes()->create([
                'ruta' => $ruta,
                'es_principal' => $esPrincipal,
            ]);
        });

        return back()->with('success', 'Imagen asociada correctamente.');
    }

    public function destroy(ImagenMascota $imagenMascota): RedirectResponse
    {
        $mascota = $imagenMascota->mascota;
        $eraPrincipal = $imagenMascota->es_principal;
        $ruta = $imagenMascota->ruta;
        $imagenMascota->delete();

        if (str_starts_with($ruta, 'img/mascotas/uploads/')) {
            File::delete(public_path($ruta));
        }

        if ($eraPrincipal) {
            $mascota->imagenes()->oldest()->first()?->update(['es_principal' => true]);
        }

        return back()->with('success', 'Imagen eliminada correctamente.');
    }
}
