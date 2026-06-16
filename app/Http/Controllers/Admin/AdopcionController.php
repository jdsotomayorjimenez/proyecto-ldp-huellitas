<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreAdopcionRequest;
use App\Models\Adopcion;
use App\Models\SolicitudAdopcion;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AdopcionController extends Controller
{
    public function index(): View
    {
        return view('admin.adopciones.index', [
            'adopciones' => Adopcion::with(['solicitudAdopcion.usuario', 'solicitudAdopcion.mascota'])
                ->latest()
                ->paginate(15),
        ]);
    }

    public function show(Adopcion $adopcion): View
    {
        $adopcion->load([
            'solicitudAdopcion.usuario',
            'solicitudAdopcion.mascota.raza.tipoMascota',
            'solicitudAdopcion.cita',
        ]);

        return view('admin.adopciones.show', [
            'adopcion' => $adopcion,
            'solicitud' => $adopcion->solicitudAdopcion,
        ]);
    }

    public function store(StoreAdopcionRequest $request, SolicitudAdopcion $solicitud): RedirectResponse
    {
        if ($solicitud->estado !== 'aprobada') {
            return back()->with('error', 'Solo puedes registrar la adopción de una solicitud aprobada.');
        }

        if ($solicitud->adopcion()->exists()) {
            return back()->with('error', 'Esta solicitud ya tiene una adopción registrada.');
        }

        if (! $solicitud->cita || $solicitud->cita->estado !== 'completada') {
            return back()->with('error', 'Completa la cita antes de registrar la adopcion final.');
        }

        $obligatoriosPendientes = $solicitud->cumplimientosRequisitos()
            ->whereHas('requisitoAdopcion', fn ($query) => $query->where('obligatorio', true))
            ->whereNotIn('estado', ['cumplido', 'no_aplica'])
            ->exists();

        if ($obligatoriosPendientes) {
            return back()->with('error', 'Faltan requisitos obligatorios por marcar como cumplidos o no aplica.');
        }

        $datos = $request->validated();
        $adopcion = null;

        DB::transaction(function () use ($datos, $solicitud, &$adopcion) {
            $adopcion = Adopcion::create([
                'solicitud_adopcion_id' => $solicitud->id,
                'fecha_adopcion' => $datos['fecha_adopcion'],
                'numero_acta' => $datos['numero_acta'],
                'observaciones' => $datos['observaciones'] ?? null,
            ]);

            $solicitud->mascota->update(['estado' => 'adoptada']);
            $solicitud->cita?->update(['estado' => 'completada']);
        });

        return redirect()
            ->route('admin.adopciones.show', $adopcion)
            ->with('success', '¡Adopción registrada con éxito! 🎉');
    }
}
