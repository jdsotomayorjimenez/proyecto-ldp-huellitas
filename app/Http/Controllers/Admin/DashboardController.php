<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Mascota;
use App\Models\Raza;
use App\Models\RequisitoAdopcion;
use App\Models\TipoMascota;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        return view('admin.dashboard', [
            'totalTipos' => TipoMascota::count(),
            'totalRazas' => Raza::count(),
            'totalMascotas' => Mascota::count(),
            'mascotasDisponibles' => Mascota::where('estado', 'disponible')->count(),
            'totalRequisitos' => RequisitoAdopcion::count(),
        ]);
    }
}
