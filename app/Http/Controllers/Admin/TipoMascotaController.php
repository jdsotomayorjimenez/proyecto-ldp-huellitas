<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreTipoMascotaRequest;
use App\Http\Requests\Admin\UpdateTipoMascotaRequest;
use App\Models\TipoMascota;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class TipoMascotaController extends Controller
{
    public function index(): View
    {
        return view('admin.tipos_mascotas.index', [
            'tipos' => TipoMascota::withCount(['razas', 'requisitosAdopcion'])
                ->orderBy('nombre')
                ->paginate(10),
        ]);
    }

    public function create(): View
    {
        return view('admin.tipos_mascotas.create');
    }

    public function store(StoreTipoMascotaRequest $request): RedirectResponse
    {
        TipoMascota::create($request->validated());

        return redirect()
            ->route('admin.tipos-mascotas.index')
            ->with('success', 'Tipo de mascota creado correctamente.');
    }

    public function edit(TipoMascota $tipo): View
    {
        return view('admin.tipos_mascotas.edit', ['tipo' => $tipo]);
    }

    public function update(
        UpdateTipoMascotaRequest $request,
        TipoMascota $tipo,
    ): RedirectResponse {
        $tipo->update($request->validated());

        return redirect()
            ->route('admin.tipos-mascotas.index')
            ->with('success', 'Tipo de mascota actualizado correctamente.');
    }

    public function destroy(TipoMascota $tipo): RedirectResponse
    {
        if ($tipo->razas()->exists() || $tipo->requisitosAdopcion()->exists()) {
            return back()->with(
                'error',
                'No se puede eliminar un tipo que tiene razas o requisitos asociados.',
            );
        }

        $tipo->delete();

        return back()->with('success', 'Tipo de mascota eliminado correctamente.');
    }
}
