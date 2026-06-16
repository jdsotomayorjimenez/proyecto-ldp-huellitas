<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreRazaRequest;
use App\Http\Requests\Admin\UpdateRazaRequest;
use App\Models\Raza;
use App\Models\TipoMascota;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RazaController extends Controller
{
    public function index(Request $request): View
    {
        $tipos = TipoMascota::withCount('razas')
            ->orderBy('nombre')
            ->get();
        $tipoSeleccionado = $tipos->firstWhere('id', $request->integer('tipo_mascota_id'))
            ?? $tipos->first();

        return view('admin.razas.index', [
            'tipos' => $tipos,
            'tipoSeleccionado' => $tipoSeleccionado,
            'razas' => Raza::with('tipoMascota')
                ->withCount('mascotas')
                ->when($tipoSeleccionado, fn ($query) => $query->where(
                    'tipo_mascota_id',
                    $tipoSeleccionado->id,
                ))
                ->orderBy('nombre')
                ->get(),
        ]);
    }

    public function create(): View
    {
        return view('admin.razas.create', [
            'tipos' => TipoMascota::orderBy('nombre')->get(),
        ]);
    }

    public function store(StoreRazaRequest $request): RedirectResponse
    {
        Raza::create($request->validated());

        return redirect()
            ->route('admin.razas.index')
            ->with('success', 'Raza creada correctamente.');
    }

    public function edit(Raza $raza): View
    {
        return view('admin.razas.edit', [
            'raza' => $raza,
            'tipos' => TipoMascota::orderBy('nombre')->get(),
        ]);
    }

    public function update(UpdateRazaRequest $request, Raza $raza): RedirectResponse
    {
        $raza->update($request->validated());

        return redirect()
            ->route('admin.razas.index')
            ->with('success', 'Raza actualizada correctamente.');
    }

    public function destroy(Raza $raza): RedirectResponse
    {
        if ($raza->mascotas()->exists()) {
            return back()->with(
                'error',
                'No se puede eliminar una raza que tiene mascotas asociadas.',
            );
        }

        $raza->delete();

        return back()->with('success', 'Raza eliminada correctamente.');
    }
}
