<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreRazaRequest;
use App\Http\Requests\Admin\UpdateRazaRequest;
use App\Models\Raza;
use App\Models\TipoMascota;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class RazaController extends Controller
{
    public function index(): View
    {
        return view('admin.razas.index', [
            'razas' => Raza::with('tipoMascota')
                ->withCount('mascotas')
                ->orderBy('nombre')
                ->paginate(10),
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
