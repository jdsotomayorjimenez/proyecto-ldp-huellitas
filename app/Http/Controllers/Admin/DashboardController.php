<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Adopcion;
use App\Models\Mascota;
use App\Models\Raza;
use App\Models\RequisitoAdopcion;
use App\Models\SolicitudAdopcion;
use App\Models\TipoMascota;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        return view('admin.dashboard', [
            // KPIs
            'totalTipos' => TipoMascota::count(),
            'totalRazas' => Raza::count(),
            'totalMascotas' => Mascota::count(),
            'mascotasDisponibles' => Mascota::where('estado', 'disponible')->count(),
            'totalRequisitos' => RequisitoAdopcion::count(),
            'solicitudesPendientes' => SolicitudAdopcion::where('estado', 'pendiente')->count(),
            'totalAdopciones' => Adopcion::count(),

            // Gráficos
            'adopcionesPorMes' => $this->adopcionesPorMes(),
            'mascotasPorEstado' => $this->mascotasPorEstado(),
            'mascotasPorTipo' => $this->mascotasPorTipo(),
            'solicitudesPorEstado' => $this->solicitudesPorEstado(),
        ]);
    }

    /**
     * Adopciones registradas en el último mes (últimas 4 semanas, por semana).
     *
     * @return array{labels: array<int, string>, data: array<int, int>}
     */
    private function adopcionesPorMes(): array
    {
        $semanas = collect(range(3, 0))
            ->map(fn (int $i): Carbon => now()->startOfWeek()->subWeeks($i));

        return [
            'labels' => $semanas->map(fn (Carbon $ini): string => 'Sem '.$ini->format('d/m'))->all(),
            'data' => $semanas->map(fn (Carbon $ini): int => Adopcion::whereBetween('fecha_adopcion', [
                $ini->toDateString(),
                $ini->copy()->endOfWeek()->toDateString(),
            ])->count())->all(),
        ];
    }

    /**
     * Distribución de mascotas por estado.
     *
     * @return array{labels: array<int, string>, data: array<int, int>}
     */
    private function mascotasPorEstado(): array
    {
        $conteos = Mascota::select('estado', DB::raw('COUNT(*) as total'))
            ->groupBy('estado')
            ->pluck('total', 'estado');

        $estados = [
            'disponible' => 'Disponible',
            'en_proceso' => 'En proceso',
            'adoptada' => 'Adoptada',
            'no_disponible' => 'No disponible',
        ];

        return [
            'labels' => array_values($estados),
            'data' => collect($estados)->keys()
                ->map(fn (string $clave): int => (int) ($conteos[$clave] ?? 0))
                ->all(),
        ];
    }

    /**
     * Cantidad de mascotas por tipo de mascota.
     *
     * @return array{labels: array<int, string>, data: array<int, int>}
     */
    private function mascotasPorTipo(): array
    {
        $conteos = TipoMascota::query()
            ->leftJoin('razas', 'razas.tipo_mascota_id', '=', 'tipos_mascotas.id')
            ->leftJoin('mascotas', 'mascotas.raza_id', '=', 'razas.id')
            ->groupBy('tipos_mascotas.id', 'tipos_mascotas.nombre')
            ->orderBy('tipos_mascotas.nombre')
            ->selectRaw('tipos_mascotas.nombre as nombre, COUNT(mascotas.id) as total')
            ->pluck('total', 'nombre');

        return [
            'labels' => $conteos->keys()->all(),
            'data' => $conteos->values()->map(fn ($total): int => (int) $total)->all(),
        ];
    }

    /**
     * Distribución de solicitudes de adopción por estado.
     *
     * @return array{labels: array<int, string>, data: array<int, int>}
     */
    private function solicitudesPorEstado(): array
    {
        $conteos = SolicitudAdopcion::select('estado', DB::raw('COUNT(*) as total'))
            ->groupBy('estado')
            ->pluck('total', 'estado');

        $estados = [
            'pendiente' => 'Pendiente',
            'aprobada' => 'Aprobada',
            'rechazada' => 'Rechazada',
            'cancelada' => 'Cancelada',
        ];

        return [
            'labels' => array_values($estados),
            'data' => collect($estados)->keys()
                ->map(fn (string $clave): int => (int) ($conteos[$clave] ?? 0))
                ->all(),
        ];
    }
}
