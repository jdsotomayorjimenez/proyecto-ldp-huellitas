<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSolicitudAdopcionRequest;
use App\Models\Adopcion;
use App\Models\Mascota;
use App\Models\SolicitudAdopcion;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class SolicitudAdopcionController extends Controller
{
    public function create(Mascota $mascota): View|RedirectResponse
    {
        if ($redirect = $this->bloquearSiNoSolicitable($mascota)) {
            return $redirect;
        }

        $mascota->load('raza.tipoMascota');

        return view('public.solicitudes.create', [
            'mascota' => $mascota,
        ]);
    }

    public function store(StoreSolicitudAdopcionRequest $request, Mascota $mascota): RedirectResponse
    {
        if ($redirect = $this->bloquearSiNoSolicitable($mascota)) {
            return $redirect;
        }

        DB::transaction(function () use ($request, $mascota) {
            SolicitudAdopcion::create([
                ...$request->validated(),
                'user_id' => $request->user()->id,
                'mascota_id' => $mascota->id,
                'estado' => 'pendiente',
                'fecha_solicitud' => now(),
            ]);

            $mascota->update(['estado' => 'en_proceso']);
        });

        return redirect()
            ->route('solicitudes.mis')
            ->with('success', "Tu solicitud para adoptar a {$mascota->nombre} fue enviada. Te avisaremos cuando el refugio la revise.");
    }

    public function misSolicitudes(): View
    {
        $solicitudes = SolicitudAdopcion::with([
            'mascota.raza.tipoMascota',
            'mascota.imagenes',
            'respuesta',
            'cita',
            'adopcion',
        ])
            ->where('user_id', auth()->id())
            ->latest()
            ->get();

        return view('public.solicitudes.mis', [
            'solicitudes' => $solicitudes,
        ]);
    }

    public function cancelar(SolicitudAdopcion $solicitud): RedirectResponse
    {
        if ($solicitud->user_id !== auth()->id()) {
            abort(403);
        }

        if ($solicitud->estado !== 'pendiente') {
            return back()->with('error', 'Solo puedes cancelar solicitudes que estén en estado pendiente.');
        }

        DB::transaction(function () use ($solicitud) {
            $solicitud->update(['estado' => 'cancelada']);
            
            // Si la mascota no tiene otras solicitudes activas, vuelve a estar disponible
            $otrasActivas = SolicitudAdopcion::where('mascota_id', $solicitud->mascota_id)
                ->whereIn('estado', ['pendiente', 'aprobada'])
                ->where('id', '!=', $solicitud->id)
                ->exists();

            if (!$otrasActivas) {
                $solicitud->mascota->update(['estado' => 'disponible']);
            }
        });

        return back()->with('success', 'Solicitud cancelada con éxito.');
    }

    public function certificado(Adopcion $adopcion): View
    {
        if ($adopcion->solicitudAdopcion->user_id !== auth()->id()) {
            abort(403);
        }

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

    /**
     * Reglas de negocio: la mascota debe estar disponible y el usuario no
     * puede haberla solicitado antes.
     */
    private function bloquearSiNoSolicitable(Mascota $mascota): ?RedirectResponse
    {
        if ($mascota->estado !== 'disponible') {
            return redirect()
                ->route('mascotas.show.public', $mascota)
                ->with('error', 'Esta mascota ya no está disponible para adopción.');
        }

        $yaSolicitada = $mascota->solicitudesAdopcion()
            ->where('user_id', auth()->id())
            ->exists();

        if ($yaSolicitada) {
            return redirect()
                ->route('solicitudes.mis')
                ->with('error', 'Ya enviaste una solicitud para esta mascota.');
        }

        return null;
    }
}
