<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreRequisitoAdopcionRequest;
use App\Http\Requests\Admin\UpdateRequisitoAdopcionRequest;
use App\Models\CumplimientoRequisito;
use App\Models\RequisitoAdopcion;
use App\Models\TipoMascota;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class RequisitoAdopcionController extends Controller
{
    public function index(Request $request): View
    {
        $tipos = TipoMascota::orderBy('nombre')->get();

        // Sin tipo en la URL => vista general (requisitos que aplican a todos los tipos).
        $tipoSeleccionado = $tipos->firstWhere('id', $request->integer('tipo_mascota_id'));
        $esGeneral = $tipoSeleccionado === null;

        $consulta = RequisitoAdopcion::with('tipoMascota')
            ->withCount('cumplimientos')
            ->when($tipoSeleccionado, fn ($query) => $query->where(
                'tipo_mascota_id',
                $tipoSeleccionado->id,
            ))
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
            ->orderBy('nombre');

        if ($esGeneral) {
            // Generales: el mismo requisito (por nombre) existe en todos los tipos.
            $totalTipos = $tipos->count();
            $requisitos = $consulta->get()
                ->groupBy('nombre')
                ->filter(fn ($grupo) => $grupo->pluck('tipo_mascota_id')->unique()->count() >= $totalTipos)
                ->map(fn ($grupo) => $grupo->first())
                ->values();
        } else {
            $requisitos = $consulta->get();
        }

        return view('admin.requisitos.index', [
            'requisitos' => $requisitos,
            'tipos' => $tipos,
            'tipoSeleccionado' => $tipoSeleccionado,
            'esGeneral' => $esGeneral,
            'nombresRequisitos' => RequisitoAdopcion::query()
                ->when($tipoSeleccionado, fn ($query) => $query->where(
                    'tipo_mascota_id',
                    $tipoSeleccionado->id,
                ))
                ->orderBy('nombre')
                ->distinct()
                ->pluck('nombre'),
        ]);
    }

    public function editGeneral(RequisitoAdopcion $requisito): View
    {
        return view('admin.requisitos.edit-general', [
            'requisito' => $requisito,
            'tiposAfectados' => RequisitoAdopcion::where('nombre', $requisito->nombre)
                ->distinct('tipo_mascota_id')
                ->count('tipo_mascota_id'),
        ]);
    }

    public function updateGeneral(Request $request, RequisitoAdopcion $requisito): RedirectResponse
    {
        $datos = $request->validate([
            'nombre' => ['required', 'string', 'max:100'],
            'descripcion' => ['nullable', 'string', 'max:255'],
            'estado' => ['required', Rule::in(['activo', 'inactivo'])],
        ]);
        $obligatorio = $request->boolean('obligatorio');

        RequisitoAdopcion::where('nombre', $requisito->nombre)->update([
            'nombre' => $datos['nombre'],
            'descripcion' => $datos['descripcion'] ?? null,
            'estado' => $datos['estado'],
            'obligatorio' => $obligatorio,
        ]);

        return redirect()
            ->route('admin.requisitos.index')
            ->with('success', 'Requisito general actualizado en todos los tipos.');
    }

    public function destroyGeneral(RequisitoAdopcion $requisito): RedirectResponse
    {
        $ids = RequisitoAdopcion::where('nombre', $requisito->nombre)->pluck('id');

        if (CumplimientoRequisito::whereIn('requisito_adopcion_id', $ids)->exists()) {
            return back()->with(
                'error',
                'No se puede eliminar: algún tipo ya tiene revisiones asociadas a este requisito.',
            );
        }

        RequisitoAdopcion::whereIn('id', $ids)->delete();

        return back()->with('success', 'Requisito general eliminado de todos los tipos.');
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
