<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CitaAdopcion;
use App\Models\CumplimientoRequisito;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class CitaAdopcionController extends Controller
{
    public function index(): View
    {
        return view('admin.citas.index', [
            'citas' => CitaAdopcion::with(['solicitudAdopcion.usuario', 'solicitudAdopcion.mascota'])
                ->orderByRaw("CASE estado
                    WHEN 'programada' THEN 1
                    WHEN 'completada' THEN 2
                    WHEN 'cancelada' THEN 3
                    ELSE 4 END")
                ->orderByDesc('fecha')
                ->paginate(15),
        ]);
    }

    public function show(CitaAdopcion $cita): View
    {
        $cita->load([
            'solicitudAdopcion.usuario',
            'solicitudAdopcion.mascota.raza.tipoMascota',
            'solicitudAdopcion.adopcion',
            'solicitudAdopcion.cumplimientosRequisitos.requisitoAdopcion',
        ]);

        $solicitud = $cita->solicitudAdopcion;

        $obligatoriosPendientes = $solicitud->cumplimientosRequisitos
            ->filter(fn (CumplimientoRequisito $c) => $c->requisitoAdopcion->obligatorio
                && ! in_array($c->estado, ['cumplido', 'no_aplica'], true))
            ->count();

        return view('admin.citas.show', [
            'cita' => $cita,
            'solicitud' => $solicitud,
            'obligatoriosPendientes' => $obligatoriosPendientes,
        ]);
    }

    public function update(Request $request, CitaAdopcion $cita): RedirectResponse
    {
        $datos = $request->validate([
            'estado' => ['required', 'in:programada,completada,cancelada'],
        ]);

        if ($cita->solicitudAdopcion->adopcion()->exists()) {
            return back()->with('error', 'Esta cita ya tiene una adopcion registrada.');
        }

        if ($datos['estado'] === 'completada') {
            $pendientes = $cita->solicitudAdopcion
                ->cumplimientosRequisitos()
                ->whereHas('requisitoAdopcion', fn ($query) => $query->where('obligatorio', true))
                ->whereNotIn('estado', ['cumplido', 'no_aplica'])
                ->exists();

            if ($pendientes) {
                return back()->with('error', 'Verifica todos los requisitos obligatorios antes de completar la cita.');
            }
        }

        DB::transaction(function () use ($cita, $datos): void {
            $cita->update(['estado' => $datos['estado']]);

            // Cancelar la cita (p. ej. si no se cumplen los requisitos) anula el
            // proceso: la solicitud queda cancelada y la mascota vuelve al catálogo.
            if ($datos['estado'] === 'cancelada') {
                $solicitud = $cita->solicitudAdopcion;
                $solicitud->update(['estado' => 'cancelada']);
                $solicitud->mascota->update(['estado' => 'disponible']);
            }
        });

        $mensaje = $datos['estado'] === 'cancelada'
            ? 'Cita cancelada. La solicitud se anuló y la mascota volvió a estar disponible.'
            : 'Estado de la cita actualizado.';

        return back()->with('success', $mensaje);
    }
}
