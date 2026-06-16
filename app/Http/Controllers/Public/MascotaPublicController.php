<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Mascota;
use App\Models\RequisitoAdopcion;
use App\Models\TipoMascota;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MascotaPublicController extends Controller
{
    /**
     * Estados que se muestran en el catálogo público.
     * Solo las mascotas disponibles: las que están en proceso o adoptadas
     * se ocultan (una mascota = una solicitud activa a la vez).
     */
    private const ESTADOS_VISIBLES = ['disponible'];

    public function home(): View
    {
        // "Los más peques": variedad de tipos, mostrando el más joven de cada uno.
        $cuposPorTipo = ['Perro' => 2, 'Gato' => 1, 'Hámster' => 1, 'Conejo' => 1, 'Ave' => 1];

        $destacadas = collect($cuposPorTipo)
            ->flatMap(fn (int $cantidad, string $tipo) => Mascota::with(['raza.tipoMascota', 'imagenes'])
                ->where('estado', 'disponible')
                ->whereHas('raza.tipoMascota', fn ($query) => $query->where('nombre', $tipo))
                ->orderByRaw('fecha_nacimiento IS NULL')
                ->orderByDesc('fecha_nacimiento')
                ->take($cantidad)
                ->get())
            ->values();

        return view('public.home', [
            'destacadas' => $destacadas,
            'totalDisponibles' => Mascota::where('estado', 'disponible')->count(),
            'totalAdoptadas' => Mascota::where('estado', 'adoptada')->count(),
            'totalTipos' => TipoMascota::count(),
        ]);
    }

    public function catalogo(Request $request): View
    {
        $tipo = $request->integer('tipo') ?: null;
        $genero = $request->input('genero') ?: null;
        $busqueda = $request->input('q') ?: null;
        $orden = $request->input('orden');

        $mascotas = Mascota::query()
            ->with(['raza.tipoMascota', 'imagenes'])
            ->whereIn('estado', self::ESTADOS_VISIBLES)
            ->when($tipo, fn ($query) => $query->whereHas('raza', fn ($raza) => $raza->where('tipo_mascota_id', $tipo)))
            ->when($genero, fn ($query) => $query->where('genero', $genero))
            ->when($busqueda, fn ($query) => $query->where('nombre', 'like', '%'.$busqueda.'%'))
            ->when($orden === 'edad_asc', fn ($query) => $query
                ->orderByRaw('fecha_nacimiento IS NULL')
                ->orderByDesc('fecha_nacimiento'))
            ->when($orden === 'edad_desc', fn ($query) => $query
                ->orderByRaw('fecha_nacimiento IS NULL')
                ->orderBy('fecha_nacimiento'))
            ->when(! in_array($orden, ['edad_asc', 'edad_desc'], true), fn ($query) => $query
                ->latest())
            ->paginate(9)
            ->withQueryString();

        return view('public.mascotas.index', [
            'mascotas' => $mascotas,
            'tipos' => TipoMascota::orderBy('nombre')->get(),
            'tipoSeleccionado' => $tipo,
            'generoSeleccionado' => $genero,
            'busqueda' => $busqueda,
            'orden' => $orden,
        ]);
    }

    public function show(Mascota $mascota): View
    {
        $mascota->load(['raza.tipoMascota', 'imagenes']);

        $requisitos = RequisitoAdopcion::where('tipo_mascota_id', $mascota->raza->tipo_mascota_id)
            ->where('estado', 'activo')
            ->orderByDesc('obligatorio')
            ->get();

        $yaSolicitada = false;

        if (auth()->check()) {
            $yaSolicitada = $mascota->solicitudesAdopcion()
                ->where('user_id', auth()->id())
                ->exists();
        }

        return view('public.mascotas.show', [
            'mascota' => $mascota,
            'requisitos' => $requisitos,
            'yaSolicitada' => $yaSolicitada,
        ]);
    }
}
