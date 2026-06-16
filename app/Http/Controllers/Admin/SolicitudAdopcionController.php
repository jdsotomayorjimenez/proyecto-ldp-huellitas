<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ResponderSolicitudRequest;
use App\Models\CitaAdopcion;
use App\Models\CumplimientoRequisito;
use App\Models\RequisitoAdopcion;
use App\Models\RespuestaSolicitud;
use App\Models\SolicitudAdopcion;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class SolicitudAdopcionController extends Controller
{
    /**
     * Orden de prioridad por defecto: primero las que están en proceso
     * (pendientes), luego las aceptadas, luego las rechazadas y canceladas.
     */
    private const ORDEN_ESTADOS = ['pendiente', 'aprobada', 'rechazada', 'cancelada'];

    public function index(Request $request): View
    {
        $estado = $request->input('estado');
        $estadoActivo = in_array($estado, self::ORDEN_ESTADOS, true) ? $estado : null;

        $solicitudes = SolicitudAdopcion::with(['usuario', 'mascota'])
            ->when($estadoActivo, fn ($query) => $query->where('estado', $estadoActivo))
            ->orderByRaw("CASE estado
                WHEN 'pendiente' THEN 1
                WHEN 'aprobada' THEN 2
                WHEN 'rechazada' THEN 3
                WHEN 'cancelada' THEN 4
                ELSE 5 END")
            ->latest('fecha_solicitud')
            ->paginate(15)
            ->withQueryString();

        return view('admin.solicitudes.index', [
            'solicitudes' => $solicitudes,
            'estados' => self::ORDEN_ESTADOS,
            'estadoActivo' => $estadoActivo,
        ]);
    }

    public function show(SolicitudAdopcion $solicitud): View
    {
        $solicitud->load([
            'usuario',
            'mascota.raza.tipoMascota',
            'mascota.imagenes',
            'respuesta',
            'cita',
            'adopcion',
            'cumplimientosRequisitos.requisitoAdopcion',
        ]);

        $obligatoriosPendientes = $solicitud->cumplimientosRequisitos
            ->filter(fn (CumplimientoRequisito $c) => $c->requisitoAdopcion->obligatorio
                && ! in_array($c->estado, ['cumplido', 'no_aplica'], true))
            ->count();

        // Requisitos del tipo de mascota: se muestran al aprobar, pero se
        // verifican formalmente durante la cita.
        $requisitos = RequisitoAdopcion::where('tipo_mascota_id', $solicitud->mascota->raza->tipo_mascota_id)
            ->where('estado', 'activo')
            ->orderByDesc('obligatorio')
            ->get();

        return view('admin.solicitudes.show', [
            'solicitud' => $solicitud,
            'obligatoriosPendientes' => $obligatoriosPendientes,
            'requisitos' => $requisitos,
        ]);
    }

    public function responder(ResponderSolicitudRequest $request, SolicitudAdopcion $solicitud): RedirectResponse
    {
        if ($solicitud->estado !== 'pendiente') {
            return back()->with('error', 'Esta solicitud ya fue respondida.');
        }

        $datos = $request->validated();

        $requisitos = RequisitoAdopcion::where('tipo_mascota_id', $solicitud->mascota->raza->tipo_mascota_id)
            ->where('estado', 'activo')
            ->get();

        DB::transaction(function () use ($datos, $solicitud, $request, $requisitos) {
            RespuestaSolicitud::create([
                'solicitud_adopcion_id' => $solicitud->id,
                'administrador_id' => $request->user()->id,
                'resultado' => $datos['resultado'],
                'respuesta' => $datos['respuesta'],
                'fecha_respuesta' => now(),
            ]);

            if ($datos['resultado'] === 'rechazada') {
                $solicitud->update(['estado' => 'rechazada']);
                $solicitud->mascota->update(['estado' => 'disponible']);

                return;
            }

            $solicitud->update(['estado' => 'aprobada']);

            // Aprobar solo agenda la cita; los requisitos se verifican despues.
            CitaAdopcion::create([
                'solicitud_adopcion_id' => $solicitud->id,
                'fecha' => $datos['fecha'],
                'hora' => $datos['hora'],
                'lugar' => $datos['lugar'],
                'indicaciones' => $datos['indicaciones'] ?? null,
                'estado' => 'programada',
            ]);

            foreach ($requisitos as $requisito) {
                CumplimientoRequisito::create([
                    'solicitud_adopcion_id' => $solicitud->id,
                    'requisito_adopcion_id' => $requisito->id,
                    'estado' => 'pendiente',
                ]);
            }
        });

        $mensaje = $datos['resultado'] === 'aprobada'
            ? 'Solicitud aprobada y cita agendada. Los requisitos se verificaran durante la cita.'
            : 'Solicitud rechazada. La mascota volvió a estar disponible.';

        return redirect()
            ->route('admin.solicitudes.show', $solicitud)
            ->with('success', $mensaje);
    }
}
