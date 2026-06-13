<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreMascotaRequest;
use App\Http\Requests\Admin\UpdateMascotaRequest;
use App\Models\Mascota;
use App\Models\Raza;
use App\Models\TipoMascota;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class MascotaController extends Controller
{
    public function index(): View
    {
        return view('admin.mascotas.index', [
            'mascotas' => Mascota::with(['raza.tipoMascota', 'imagenes'])
                ->orderByDesc('created_at')
                ->paginate(10),
        ]);
    }

    public function create(): View
    {
        return view('admin.mascotas.create', [
            'razas' => $this->razasDisponibles(),
            'tipos' => TipoMascota::orderBy('nombre')->get(),
        ]);
    }

    public function store(StoreMascotaRequest $request): RedirectResponse
    {
        $mascota = Mascota::create($request->validated());

        return redirect()
            ->route('admin.mascotas.edit', $mascota)
            ->with('success', 'Mascota creada. Ahora puedes asociar sus imágenes.');
    }

    public function edit(Mascota $mascota): View
    {
        $mascota->load('imagenes');

        return view('admin.mascotas.edit', [
            'mascota' => $mascota,
            'razas' => $this->razasDisponibles(),
            'tipos' => TipoMascota::orderBy('nombre')->get(),
        ]);
    }

    public function update(UpdateMascotaRequest $request, Mascota $mascota): RedirectResponse
    {
        $mascota->update($request->validated());

        return redirect()
            ->route('admin.mascotas.index')
            ->with('success', 'Mascota actualizada correctamente.');
    }

    public function destroy(Mascota $mascota): RedirectResponse
    {
        if ($mascota->solicitudesAdopcion()->exists()) {
            return back()->with(
                'error',
                'No se puede eliminar una mascota que tiene solicitudes asociadas.',
            );
        }

        $mascota->delete();

        return back()->with('success', 'Mascota eliminada correctamente.');
    }

    private function razasDisponibles()
    {
        return Raza::with('tipoMascota')
            ->get()
            ->sortBy(fn (Raza $raza) => $raza->tipoMascota->nombre.' '.$raza->nombre)
            ->values();
    }
}
