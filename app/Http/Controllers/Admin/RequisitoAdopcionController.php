<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreRequisitoAdopcionRequest;
use App\Http\Requests\Admin\UpdateRequisitoAdopcionRequest;
use App\Models\RequisitoAdopcion;
use App\Models\TipoMascota;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class RequisitoAdopcionController extends Controller
{
    public function index(Request $request): View
    {
        $requisitos = RequisitoAdopcion::with('tipoMascota')
            ->withCount('cumplimientos')
            ->when(
                $request->filled('tipo_mascota_id'),
                fn ($query) => $query->where(
                    'tipo_mascota_id',
                    $request->integer('tipo_mascota_id'),
                ),
            )
            ->when(
                $request->filled('estado'),
                fn ($query) => $query->where('estado', $request->string('estado')),
            )
            ->when(
                $request->filled('buscar'),
                fn ($query) => $query->where(
                    fn ($subquery) => $subquery
                        ->where('nombre', 'like', '%'.$request->string('buscar')->trim().'%')
                        ->orWhere('descripcion', 'like', '%'.$request->string('buscar')->trim().'%'),
                ),
            )
            ->orderBy('nombre')
            ->orderBy('tipo_mascota_id')
            ->paginate(10)
            ->withQueryString();

        return view('admin.requisitos.index', [
            'requisitos' => $requisitos,
            'tipos' => TipoMascota::orderBy('nombre')->get(),
        ]);
    }

    public function create(): View
    {
        return view('admin.requisitos.create', [
            'tipos' => TipoMascota::orderBy('nombre')->get(),
        ]);
    }

    public function store(StoreRequisitoAdopcionRequest $request): RedirectResponse
    {
        $datos = $request->validated();

        DB::transaction(function () use ($datos): void {
            if ($datos['alcance'] === 'general') {
                foreach ($datos['tipos'] as $tipoId) {
                    RequisitoAdopcion::create([
                        'tipo_mascota_id' => $tipoId,
                        'nombre' => $datos['nombre'],
                        'descripcion' => $datos['descripcion'] ?? null,
                        'obligatorio' => (bool) ($datos['obligatorios'][$tipoId] ?? false),
                        'estado' => $datos['estado'],
                    ]);
                }

                return;
            }

            RequisitoAdopcion::create([
                'tipo_mascota_id' => $datos['tipo_mascota_id'],
                'nombre' => $datos['nombre'],
                'descripcion' => $datos['descripcion'] ?? null,
                'obligatorio' => $datos['obligatorio'],
                'estado' => $datos['estado'],
            ]);
        });

        return redirect()
            ->route('admin.requisitos.index')
            ->with(
                'success',
                $datos['alcance'] === 'general'
                    ? 'Requisito general creado para todos los tipos.'
                    : 'Requisito creado correctamente.',
            );
    }

    public function edit(RequisitoAdopcion $requisito): View
    {
        return view('admin.requisitos.edit', [
            'requisito' => $requisito,
            'tipos' => TipoMascota::orderBy('nombre')->get(),
        ]);
    }

    public function update(
        UpdateRequisitoAdopcionRequest $request,
        RequisitoAdopcion $requisito,
    ): RedirectResponse {
        $requisito->update($request->validated());

        return redirect()
            ->route('admin.requisitos.index')
            ->with('success', 'Requisito actualizado correctamente.');
    }

    public function destroy(RequisitoAdopcion $requisito): RedirectResponse
    {
        if ($requisito->cumplimientos()->exists()) {
            return back()->with(
                'error',
                'No se puede eliminar un requisito que ya tiene revisiones asociadas.',
            );
        }

        $requisito->delete();

        return back()->with('success', 'Requisito eliminado correctamente.');
    }
}
